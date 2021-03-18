<?php declare(strict_types = 1);

namespace PdTests\CssDependenciesVersion\CssDependenciesVersion;

require __DIR__ . '/../bootstrap.php';

final class ProcessTest extends \Tester\TestCase
{

	public function getTestUrlData(): array
	{
		return [
			['url("images/image.png")', 'url("images/image.png")'],
			['url("/images/image.png")', 'url("/images/image.png?v=fakeVersion")'],
			['url("/images/image.png?v=version")', 'url("/images/image.png?v=fakeVersion")'],
			['url("/images/image.png?parameter=parameterValue")', 'url("/images/image.png?parameter=parameterValue&v=fakeVersion")'],
			['url("/images/image.png?parameter=parameterValue&secondParameter=secondParameterValue")', 'url("/images/image.png?parameter=parameterValue&secondParameter=secondParameterValue&v=fakeVersion")'],
			["url('/images/image.png?#iefix')", "url('/images/image.png?v=fakeVersion#iefix')"],
			["url(data:image/gif;base64,R0lGODlhEAA)", "url(data:image/gif;base64,R0lGODlhEAA)"],
		];
	}


	/**
	 * @dataProvider getTestUrlData
	 */
	public function testUrl(string $input, string $expected): void
	{
		$version = new class implements \Pd\CssDependenciesVersion\IVersion
		{

			public function version(string $url): string
			{
				return 'fakeVersion';
			}
		};
		$logger = new \Psr\Log\NullLogger();
		$cssDependencyVersion = new \Pd\CssDependenciesVersion\CssDependenciesVersion($version, $logger, 'v');

		$result = $cssDependencyVersion->process($input);

		\Tester\Assert::type('string', $result);
		\Tester\Assert::equal($expected, $result);
	}
}

(new ProcessTest())->run();
