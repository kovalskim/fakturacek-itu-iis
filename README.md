# Webová aplikace "Fakturáček"

## 1. Popis instalace
### 1.1 Požadavky
- PHP 7.4
- MYSQL databáze
- GD extension
- mbstring extension

### 1.2 Postup
1. Rozbalit archiv do složky na server.
2. V dané složce spustit nástroj composer příkazem `composer update`, který nainstaluje všechny potřebné závislosti.
3. Dále je potřeba povolit webovému serveru zápis do složek `temp/cache` a `log`.
4. Také je potřeba vytvořit databázi s názvem "fakturacek". Do ní naimportovat dump databáze umístěný ve archívu (`fakturacek.sql`).
5. Do souboru config/local.neon je potřeba zadat přihlašovací údaje k databázi.

## 2. Další knihovny
Vše potřebné pro projekt se nainstaluje pomocí příkazu `composer update` (viz popis instalace).

- [Nette](https://doc.nette.org/cs/3.1/)
- [Nextras/dbal](https://nextras.org/dbal/docs/master/)
- [Nextras/forms-rendering](https://github.com/nextras/forms-rendering)
- [Nextras/secured-links](https://nextras.org/secured-links/docs/master/)
- [Nextras/datagrid](https://nextras.org/datagrid/docs/master/)
- [Contributte/forms-multiplier](https://contributte.org/packages/contributte/forms-multiplier.html)
- [Mpdf/Mpdf](https://mpdf.github.io/)