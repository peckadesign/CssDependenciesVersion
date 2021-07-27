<?php declare(strict_types = 1);

namespace PdTests\CssDependenciesVersion\CssDependenciesVersion;

require __DIR__ . '/../../bootstrap.php';

final class ExecuteTest extends \Tester\TestCase
{

	public function testWithoutOptions(): void
	{
		$cb = function (): void {
			$loggerInterface = new \Psr\Log\NullLogger();
			$output = new \Symfony\Component\Console\Output\BufferedOutput();
			$absoluteUrlResolver = new \Pd\Version\Resolvers\AbsoluteUrlResolver();
			$relativePathGetter = new class implements \Pd\Version\Resolvers\Getter\RelativePathGetterInterface {

				public function getFileName(string $directory, string $path): ?string
				{

				}

			};
			$pathResolver = new \Pd\Version\Resolvers\PathResolver(FALSE, $relativePathGetter);
			$command = new \Pd\CssDependenciesVersion\Commands\VersionCommand($loggerInterface, 'v', $absoluteUrlResolver, $pathResolver);

			$input = new \Symfony\Component\Console\Input\ArrayInput([]);

			$command->run($input, $output);
		};
		\Tester\Assert::exception($cb, \Symfony\Component\Console\Exception\RuntimeException::class);
	}


	public function testWithoutArguments(): void
	{
		$cb = function (): void {
			$loggerInterface = new \Psr\Log\NullLogger();
			$output = new \Symfony\Component\Console\Output\BufferedOutput();
			$absoluteUrlResolver = new \Pd\Version\Resolvers\AbsoluteUrlResolver();
			$relativePathGetter = new class implements \Pd\Version\Resolvers\Getter\RelativePathGetterInterface {

				public function getFileName(string $directory, string $path): ?string
				{

				}

			};
			$pathResolver = new \Pd\Version\Resolvers\PathResolver(FALSE, $relativePathGetter);
			$command = new \Pd\CssDependenciesVersion\Commands\VersionCommand($loggerInterface, 'v', $absoluteUrlResolver, $pathResolver);

			$arguments = [
				'--baseDir' => 'path',
			];

			$input = new \Symfony\Component\Console\Input\ArrayInput($arguments);

			$command->run($input, $output);
		};
		\Tester\Assert::exception($cb, \Symfony\Component\Console\Exception\RuntimeException::class, 'Not enough arguments (missing: "file").');
	}


	public function testRealFile(): void
	{
		$cssFileName = 'file.css';
		\Nette\Utils\FileSystem::delete($cssFileName);
		\Nette\Utils\FileSystem::write($cssFileName, 'body {background: url("/file.png") }');

		$loggerInterface = new \Psr\Log\NullLogger();
		$output = new \Symfony\Component\Console\Output\BufferedOutput();
		$absoluteUrlResolver = new \Pd\Version\Resolvers\AbsoluteUrlResolver();
		$relativePathGetter = new class implements \Pd\Version\Resolvers\Getter\RelativePathGetterInterface {

			public function getFileName(string $directory, string $path): ?string
			{
				\Tester\Assert::equal('path', $directory);
				\Tester\Assert::equal('/file.png', $path);

				return __DIR__ . $path;
			}

		};
		$pathResolver = new \Pd\Version\Resolvers\PathResolver(FALSE, $relativePathGetter);
		$command = new \Pd\CssDependenciesVersion\Commands\VersionCommand($loggerInterface, 'v', $absoluteUrlResolver, $pathResolver);

		$arguments = [
			'file' => $cssFileName,
			'--baseDir' => 'path',
		];
		$input = new \Symfony\Component\Console\Input\ArrayInput($arguments, $command->getDefinition());

		try {
			$command->run($input, $output);
			\Tester\Assert::true(\Tester\Assert::isMatching('~^body {background: url\("/file\.png\?v=[a-z0-9]+"\) \}$~', \file_get_contents($cssFileName), TRUE));
		} finally {
			\Nette\Utils\FileSystem::delete($cssFileName);
		}
	}

}

(new ExecuteTest())->run();
