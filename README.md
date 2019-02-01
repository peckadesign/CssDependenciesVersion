# CssDependenciesVersion

Upraví URL cesty v CSS souboru, aby všechny závislosti obsahovaly verzi detekovanou pomocí nástroje https://github.com/peckadesign/version.

Přidání do projektu s Kdyby/Console:

```
	-
		factory: Pd\CssDependenciesVersion\Commands\VersionCommand
		arguments:
			versionParameter: v
		tags:
			- kdyby.console.command
```

Po skompilování CSS je potřeba spustit `php www/index.php pd:css-dependencies-version:version --baseDir www/ www/css/styles.css`. Volba `baseDir` je cesta ke statickému obsahu, na který odkazují absolutní cesty v `url(...)` v CSS.
