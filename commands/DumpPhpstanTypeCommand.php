<?php

declare(strict_types=1);

namespace commands;

use PHPStan\Analyser\OutOfClassScope;
use PHPStan\BetterReflection\Reflection\Adapter\ReflectionProperty;
use PHPStan\DependencyInjection\ContainerFactory;
use PHPStan\DependencyInjection\MissingServiceException;
use PHPStan\Reflection\ClassMemberAccessAnswerer;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\ExtendedPropertyReflection;
use PHPStan\Reflection\MissingMethodFromReflectionException;
use PHPStan\Reflection\MissingPropertyFromReflectionException;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Type\VerbosityLevel;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DumpPhpstanTypeCommand extends Command
{
  /** @var string */
  protected static $defaultName = 'app:dump_phpstan_type';

  /** @var string */
  protected static $defaultDescription = 'Dump PHPStan type map for a fully qualified class name';

  protected function configure(): void
  {
    $this->addArgument('fqcn', InputArgument::REQUIRED, 'Fully qualified class name (e.g. WTForms\\Fields\\Core\\Field)');
    $this->addOption('output', 'o', InputOption::VALUE_OPTIONAL, 'Output file (default: stdout)', 'php://stdout');
    $this->addOption('debug', null, InputOption::VALUE_NONE, 'Show debug info for skipped properties/methods');
  }

  protected function execute(InputInterface $input, OutputInterface $output): int
  {
    $fqcn = $input->getArgument('fqcn');

    $containerFactory = new ContainerFactory(getcwd());
    $container = $containerFactory->create(
        sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'phpstan-dump',
        $this->findConfigFiles(),
        [getcwd() . DIRECTORY_SEPARATOR . 'src'],
        [],
        [],
        '5'
    );

    $debug = (bool)$input->getOption('debug');
    $outputFile = $input->getOption('output');

    /* @var ReflectionProvider $reflectionProvider */
    try {
      $reflectionProvider = $container->getByType(ReflectionProvider::class);
    } catch (MissingServiceException $e) {
      $output->writeln('<error>Failed to resolve ReflectionProvider from PHPStan container</error>');
      if ($debug) {
        $output->writeln(sprintf("<comment>%s</comment>", $e->getMessage()));
      }

      return Command::FAILURE;
    }

    if (!$reflectionProvider->hasClass($fqcn)) {
      $output->writeln(sprintf("<error>Class %s not found</error>", $fqcn));

      return Command::FAILURE;
    }

    $classReflection = $reflectionProvider->getClass($fqcn);
    $verbosity = VerbosityLevel::precise();
    $scope = new OutOfClassScope();

    if ($parent = $classReflection->getParentClass()) {
      $parent = $parent->getName();
    }
    $result = [
        'class'      => $classReflection->getName(),
        'parent'     => $parent,
        'interfaces' => array_keys($classReflection->getInterfaces()),
        'properties' => [],
        'methods'    => [],
    ];

    // Properties
    $result = $this->processProperties($classReflection, $scope, $verbosity, $result, $output, $debug);

    // Methods
    $result = $this->processMethods($classReflection, $scope, $verbosity, $result, $output, $debug);

    $json = json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

    if ('php://stdout' !== $outputFile) {
      file_put_contents($outputFile, $json . "\n");
      $output->writeln(sprintf("<info>Written to %s</info>", $outputFile));
    } else {
      $output->writeln($json);
    }

    return Command::SUCCESS;
  }

  public function processMethods(
      ClassReflection $classReflection,
      OutOfClassScope $scope,
      VerbosityLevel  $verbosity,
      array           $result,
      OutputInterface $output,
      bool            $debug
  ): array
  {
    foreach ($classReflection->getNativeReflection()->getMethods() as $method) {
      if (!$classReflection->hasMethod($method->getName())) {
        continue;
      }
      try {
        $phpstanMethod = $classReflection->getMethod($method->getName(), $scope);
        $variant = $phpstanMethod->getVariants()[0];

        $params = [];
        foreach ($variant->getParameters() as $param) {
          $params[$param->getName()] = [
              'type'     => $param->getType()->describe($verbosity),
              'optional' => $param->isOptional(),
              'variadic' => $param->isVariadic(),
          ];
        }

        if ($throwType = $phpstanMethod->getThrowType()) {
          $throwType = $throwType->describe($verbosity);
        }
        $result['methods'][$method->getName()] = [
            'returnType'     => $variant->getReturnType()->describe($verbosity),
            'throwType'      => $throwType,
            'parameters'     => $params,
            'declaringClass' => $phpstanMethod->getDeclaringClass()->getName(),
        ];
      } catch (MissingMethodFromReflectionException $e) {
        if ($debug) {
          $output->writeln(sprintf("<comment>Skipped method %s: %s</comment>", $method->getName(), $e->getMessage()));
        }
      }
    }

    return $result;
  }

  public function processProperties(
      ClassReflection $classReflection,
      OutOfClassScope $scope,
      VerbosityLevel  $verbosity,
      array           $result,
      OutputInterface $output,
      bool            $debug
  ): array
  {
    foreach ($classReflection->getNativeReflection()->getProperties() as $prop) {
      try {
        $phpstanProp = $this->resolveProperty($classReflection, $prop, $scope);
        if (null === $phpstanProp) {
          if ($debug) {
            $output->writeln(sprintf("<comment>Skipped property %s: not resolvable</comment>", $prop->getName()));
          }
          continue;
        }
        $result['properties'][$prop->getName()] = [
            'readType'       => $phpstanProp->getReadableType()->describe($verbosity),
            'writeType'      => $phpstanProp->getWritableType()->describe($verbosity),
            'declaringClass' => $phpstanProp->getDeclaringClass()->getName(),
        ];
      } catch (MissingPropertyFromReflectionException $e) {
        if ($debug) {
          $output->writeln(sprintf("<comment>Skipped property %s: %s</comment>", $prop->getName(), $e->getMessage()));
        }
      }
    }

    return $result;
  }

  /**
   * @return string[]
   */
  private function findConfigFiles(): array
  {
    $candidates = ['phpstan.neon', 'phpstan.neon.dist'];
    $configs = [];
    foreach ($candidates as $candidate) {
      $path = getcwd() . '/' . $candidate;
      if (file_exists($path)) {
        $configs[] = $path;
      }
    }

    return $configs;
  }

  /**
   * @throws MissingPropertyFromReflectionException
   */
  private function resolveProperty(
      ClassReflection           $classReflection,
      ReflectionProperty        $prop,
      ClassMemberAccessAnswerer $scope
  ): ?ExtendedPropertyReflection
  {
    if ($classReflection->hasStaticProperty($prop->getName())) {
      return $classReflection->getStaticProperty($prop->getName());
    } elseif ($classReflection->hasInstanceProperty($prop->getName())) {
      return $classReflection->getInstanceProperty($prop->getName(), $scope);
    } elseif ($classReflection->hasNativeProperty($prop->getName())) {
      return $classReflection->getNativeProperty($prop->getName());
    }

    return null;
  }
}
