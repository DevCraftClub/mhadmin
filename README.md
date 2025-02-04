[![GitHub issues](https://img.shields.io/github/issues/DevCraftClub/mhadmin.svg?style=flat-square)](https://github.com/DevCraftClub/mhadmin/issues)[![GitHub forks](https://img.shields.io/github/forks/DevCraftClub/mhadmin.svg?style=flat-square)](https://github.com/DevCraftClub/mhadmin/network)[![GitHub license](https://img.shields.io/github/license/DevCraftClub/mhadmin.svg?style=flat-square)](https://github.com/DevCraftClub/mhadmin/blob/master/LICENSE)

# MHAdmin

![Текущая версия](https://img.shields.io/github/manifest-json/v/DevCraftClub/mhadmin/main?style=for-the-badge&label=%D0%92%D0%B5%D1%80%D1%81%D0%B8%D1%8F)![Статус разработки](https://img.shields.io/badge/dynamic/json?url=https%3A%2F%2Fraw.githubusercontent.com%2FDevCraftClub%2Fmhadmin%2Frefs%2Fheads%2Fmain%2Fmanifest.json&query=%24.status&style=for-the-badge&label=%D0%A1%D1%82%D0%B0%D1%82%D1%83%D1%81&color=orange)

![Версия DLE](https://img.shields.io/badge/dynamic/json?url=https%3A%2F%2Fraw.githubusercontent.com%2FDevCraftClub%2Fmhadmin%2Frefs%2Fheads%2Fmain%2Fmanifest.json&query=%24.dle&style=for-the-badge&label=DLE)![Версия PHP](https://img.shields.io/badge/dynamic/json?url=https%3A%2F%2Fraw.githubusercontent.com%2FDevCraftClub%2Fmhadmin%2Frefs%2Fheads%2Fmain%2Fmanifest.json&query=%24.php&style=for-the-badge&logo=php&logoColor=777BB4&label=PHP&color=777BB4)

Административная панель для моих модулей для движка DLE. Основная часть функционала будет основана на CSS framework [Fomantic UI](https://fomantic-ui.com/), JS framework [jQuery](https://jquery.com/) и шаблонизаторе [Twig](https://twig.symfony.com/).


**Установка / Обновление**

1. **У вас три варианта для установки:**

1.1. **При помощи bat-Скрипта. Для пользователей Windows**

Для этого устанавливаем [7Zip](https://www.7-zip.org/download.html).
После установки запускаем скрипт install_archive.bat.
После завершения установки - загружаем install.zip в менеджер плагинов.

1.2. **Упаковать самому**

Любым архиватором запаковать всё содержимое в папке upload (нужен формат zip!), причём так, чтобы в корне архива был файл install.xml и папка engine.
Затем устанавливаем архив через менеджер плагинов.

1.3. **Просто залить**

Залейте папку engine в корень сайта и установите плагин через менеджер плагинов.