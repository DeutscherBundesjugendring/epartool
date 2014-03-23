-- phpMyAdmin SQL Dump
-- version 3.5.8.2
-- http://www.phpmyadmin.net
--
-- Host: sql297.your-server.de
-- Erstellungszeit: 20. Mrz 2014 um 08:50
-- Server Version: 5.5.35-0+wheezy1
-- PHP-Version: 5.3.10-1ubuntu3.10

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Datenbank: `epartool`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `articles`
--

CREATE TABLE IF NOT EXISTS `articles` (
  `art_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Article ID',
  `kid` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'rel KID',
  `proj` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'which project',
  `desc` varchar(44) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Readable descr for admin',
  `hid` enum('y','n') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n' COMMENT 'y=hide from public',
  `ref_nm` varchar(30) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'article reference name',
  `artcl` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'Article itself',
  `sidebar` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'Content for sidebar',
  `parent_id` smallint(5) DEFAULT NULL COMMENT 'parent article',
  PRIMARY KEY (`art_id`) USING BTREE
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AVG_ROW_LENGTH=261 CHECKSUM=1 AUTO_INCREMENT=186 ;

--
-- Daten für Tabelle `articles`
--

INSERT INTO `articles` (`art_id`, `kid`, `proj`, `desc`, `hid`, `ref_nm`, `artcl`, `sidebar`, `parent_id`) VALUES
(5, 0, 'xx', 'Datenschutz', 'n', 'privacy', '&lt;h1&gt;Datenschutz&lt;/h1&gt;\r\n\r\n&lt;p&gt;&lt;span style=&quot;color:#008080;&quot;&gt;Genau wie der Deutsche Bundesjugendring nimmt ihr den Schutz personenbezogener Daten sicher sehr ernst. So wollt ihr siche rauch, dass die Teilnehmenden wissen, wann ihr welche Daten erhebt und wie ihr sie verwendet. Einigt euch im Vorfeld auf Ma&amp;szlig;nahmen, die sicherstellen, dass die Vorschriften &amp;uuml;ber den Datenschutz sowohl von euch selbst als auch von externen Dienstleistenden beachtet werden.&lt;/span&gt;&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;span style=&quot;color:#008080;&quot;&gt;Hier findet ihr unseren Text zum Datenschutz, an dem ihr euch gerne orientieren k&amp;ouml;nnt:&lt;/span&gt;&lt;/p&gt;\r\n\r\n&lt;h3&gt;&lt;br /&gt;\r\nWelche Daten werden gesammelt und wie werden sie weiterverwendet?&lt;/h3&gt;\r\n\r\n&lt;p&gt;Die einzige Voraussetzung f&amp;uuml;r die Teilnahme an einer Online-Beteiligungsrunde unter&amp;nbsp;&lt;a href=&quot;http://abc.de&quot; target=&quot;_blank&quot;&gt;Link zu eurer ePartool-Seite&lt;/a&gt; ist eine funktionierende &lt;strong&gt;E-Mail-Adresse&lt;/strong&gt;. Diese wird nicht ver&amp;ouml;ffentlicht und auch nicht an Dritte weitergegeben. Sie wird allein dazu genutzt,&lt;/p&gt;\r\n\r\n&lt;ul&gt;\r\n	&lt;li&gt;um euch einen Link zuzuschicken, mit dem ihr eure Beitr&amp;auml;ge best&amp;auml;tigt (Verifizierung);&lt;/li&gt;\r\n	&lt;li&gt;damit ihr zu einem sp&amp;auml;teren Zeitpunkt noch einmal auf eure Beitr&amp;auml;ge zugreifen k&amp;ouml;nnt;&lt;/li&gt;\r\n	&lt;li&gt;um mit euch Kontakt aufzunehmen, sollten eure Beitr&amp;auml;ge z.B. nicht richtig &amp;uuml;bermittelt worden zu sein;&lt;/li&gt;\r\n	&lt;li&gt;um euch die Informationen f&amp;uuml;r die Teilnahme an einer Abstimmung zukommen zu lassen;&lt;/li&gt;\r\n	&lt;li&gt;um euch &amp;ndash; sofern ihr das wollt - &amp;uuml;ber die Ergebnisse einer Beteiligungsrunde und&amp;nbsp; die darauf folgenden Reaktionen zu informieren.&lt;/li&gt;\r\n&lt;/ul&gt;\r\n\r\n&lt;p&gt;Die &lt;strong&gt;Passw&amp;ouml;rter&lt;/strong&gt;, die mit der Best&amp;auml;tigungsmail verschickt werden, werden vom System automatisiert erstellt und nie im Klartext gespeichert. Aus diesem Grund k&amp;ouml;nnen Passw&amp;ouml;rter nicht wieder hergestellt, sondern nur neu vergeben werden.&lt;/p&gt;\r\n\r\n&lt;p&gt;Die &lt;strong&gt;Eingabe weiterer Daten&lt;/strong&gt;, wie Name, Alter und Gruppengr&amp;ouml;&amp;szlig;e, erfolgt &lt;strong&gt;freiwillig&lt;/strong&gt;. Diese Daten dienen dazu, uns einen &amp;Uuml;berblick zu geben, wer an der Beteiligungsrunde teilgenommen hat.&lt;/p&gt;\r\n\r\n&lt;p&gt;W&amp;auml;hrend des Eintragens werden die &lt;strong&gt;IP-Adresse&lt;/strong&gt; eures Internetzugriffs und der von euch verwendete &lt;strong&gt;Internetbrowser&lt;/strong&gt; erfasst. Diese Daten werden allerdings nur wenige Tage gespeichert und dienen dazu, euch bei Unterbrechungen den sp&amp;auml;teren Zugriff auf schon eingetragene Texte zu erm&amp;ouml;glichen sowie Spamrobots auszuschlie&amp;szlig;en.&lt;/p&gt;\r\n\r\n&lt;p&gt;Die Funktion &amp;bdquo;Unterst&amp;uuml;tzen&amp;ldquo; von anderen Beitr&amp;auml;gen generiert aus eurer IP-Adresse und dem verwendeten Browser eine Art Quersumme (&amp;bdquo;Hash&amp;ldquo;), damit jede_r einen Beitrag nur einmal &amp;bdquo;unterst&amp;uuml;tzen&amp;ldquo; kann. Eine R&amp;uuml;ckverfolgung zu eurem Rechner ist damit nicht m&amp;ouml;glich.&lt;/p&gt;\r\n\r\n&lt;p&gt;Die Daten, die beim Zugriff auf das Internetangebot &lt;a href=&quot;http://abc.de&quot; target=&quot;_blank&quot;&gt;Link zu eurer ePartool-Seite&lt;/a&gt; protokolliert worden sind, werden vom Deutschen Bundesjugendring nur an Dritte &amp;uuml;bermittelt, soweit wir gesetzlich oder durch Gerichtsentscheidung dazu verpflichtet sind oder die Weitergabe im Falle von Angriffen auf die Internetinfrastruktur zur Rechts- oder Strafverfolgung erforderlich ist. Eine Weitergabe zu anderen nicht-kommerziellen oder zu kommerziellen Zwecken erfolgt nicht.&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;strong&gt;Bitte beachtet&lt;/strong&gt;:&lt;br /&gt;\r\nDie Daten&amp;uuml;bertragung im Internet kann Sicherheitsl&amp;uuml;cken aufweisen. Ein l&amp;uuml;ckenloser Schutz der Daten vor dem Zugriff durch Dritte ist nicht m&amp;ouml;glich. Wir sind aber darum bem&amp;uuml;ht, die H&amp;uuml;rden m&amp;ouml;glichst hoch zu setzen.&lt;/p&gt;\r\n\r\n&lt;p&gt;Die Nutzung des Internetangebots &lt;a href=&quot;http://abc.de&quot; target=&quot;_blank&quot;&gt;Link zu eurer ePartool-Seite&lt;/a&gt; kann deshalb &amp;uuml;ber eine verschl&amp;uuml;sselte https-Verbindung erfolgen. Wir setzen hierzu jeweils aktuelle SSL-Zertifikate ein (Stand Juni 2013: AES-256).&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;br /&gt;\r\n&lt;strong&gt;Noch Fragen?&lt;/strong&gt;&lt;br /&gt;\r\nDann schreibt uns unter &lt;a href=&quot;mailto:abc@d.de&quot;&gt;EMAIL&lt;/a&gt; oder ruft an unter TELEFON.&lt;/p&gt;\r\n\r\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;\r\n', '', 0),
(19, 0, 'xx', 'Kontakt', 'n', 'contact', '&lt;h1&gt;&lt;a id=&quot;oben&quot; name=&quot;oben&quot;&gt;&lt;/a&gt;Kontakt&lt;/h1&gt;\r\n\r\n&lt;p&gt;&lt;a id=&quot;kontakt&quot; name=&quot;kontakt&quot;&gt;&lt;/a&gt;&lt;/p&gt;\r\n\r\n&lt;p&gt;PROJEKTNAME&lt;/p&gt;\r\n\r\n&lt;p&gt;ADRESSE&lt;/p&gt;\r\n\r\n&lt;p&gt;Telefon:&lt;br /&gt;\r\nTelefax:&lt;br /&gt;\r\nE-Mail:&amp;nbsp;&amp;nbsp;&amp;nbsp;&lt;br /&gt;\r\nInternet:&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;h4&gt;&amp;nbsp;&lt;/h4&gt;\r\n\r\n&lt;h2&gt;&lt;a id=&quot;oben&quot; name=&quot;oben&quot;&gt;&lt;/a&gt;Impressum&lt;/h2&gt;\r\n\r\n&lt;h3&gt;Herausgeber dieser Website&lt;/h3&gt;\r\n\r\n&lt;h3&gt;Verantwortlich&lt;/h3&gt;\r\n\r\n&lt;h3&gt;Redaktion&lt;/h3&gt;\r\n\r\n&lt;h3&gt;Adresse&lt;/h3&gt;\r\n\r\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;h3&gt;Kontakt&lt;/h3&gt;\r\n\r\n&lt;p&gt;E-Mail:&lt;/p&gt;\r\n\r\n&lt;p&gt;Internet:&lt;/p&gt;\r\n\r\n&lt;div&gt;&amp;nbsp;&lt;/div&gt;\r\n\r\n&lt;div&gt;\r\n&lt;h2&gt;&lt;a id=&quot;gap&quot; name=&quot;gap&quot;&gt;&lt;/a&gt;Bewusste Entscheidung zur Nutzung des Gender_Gap&lt;/h2&gt;\r\n\r\n&lt;p&gt;Das &amp;bdquo;_&amp;ldquo; ist der sogenannte Gender_Gap. Wie verwenden diese Schreibweise um deutlich zu machen, dass wir alle Menschen ansprechen m&amp;ouml;chten unabh&amp;auml;ngig von ihrer Geschlechtsidentit&amp;auml;t. Ein Gender_Gap wird eingef&amp;uuml;gt, um neben dem biologischen das soziale Geschlecht (Gender) darzustellen. Es ist eine aus dem Bereich der Queer-Theorie stammende Variante des Binnen-I. Der Gender_Gap soll ein Mittel der sprachlichen Darstellung aller sozialen Geschlechter und Geschlechtsidentit&amp;auml;ten abseits der Zweigeschlechtlichkeit sein. In der deutschen Sprache w&amp;auml;re dies sonst nur durch Umschreibungen m&amp;ouml;glich. Die Intention ist, durch den Zwischenraum einen Hinweis auf diejenigen Menschen zu geben, die nicht in das ausschlie&amp;szlig;liche Frau-Mann-Schema hineinpassen oder nicht hineinpassen wollen, wie Intersexuelle oder Transgender.&lt;/p&gt;\r\n&lt;/div&gt;\r\n\r\n&lt;h2&gt;&amp;nbsp;&lt;/h2&gt;\r\n\r\n&lt;h2&gt;&lt;a id=&quot;Rechtliches&quot; name=&quot;Rechtliches&quot;&gt;&lt;/a&gt;Rechtliche Hinweise&lt;/h2&gt;\r\n\r\n&lt;p&gt;Alle Angaben unseres Internetangebotes wurden sorgf&amp;auml;ltig gepr&amp;uuml;ft. Wir bem&amp;uuml;hen uns, dieses Informationsangebot aktuell und inhaltlich richtig sowie vollst&amp;auml;ndig anzubieten. Dennoch ist das Auftreten von Fehlern nicht v&amp;ouml;llig auszuschlie&amp;szlig;en. Eine Garantie f&amp;uuml;r die Vollst&amp;auml;ndigkeit, Richtigkeit und letzte Aktualit&amp;auml;t kann daher nicht &amp;uuml;bernommen werden.&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;br /&gt;\r\nDer Deutsche Bundesjugendring kann diese Website nach eigenem Ermessen jederzeit ohne Ank&amp;uuml;ndigung ver&amp;auml;ndern und/oder deren Betrieb einstellen. Er ist nicht verpflichtet, Inhalte dieser Website zu aktualisieren.&lt;br /&gt;\r\n&lt;br /&gt;\r\nDer Zugang und die Benutzung dieser Website geschieht auf eigene Gefahr der Benutzer_innen. Der Deutsche Bundesjugendring ist nicht verantwortlich und &amp;uuml;bernimmt keinerlei Haftung f&amp;uuml;r Sch&amp;auml;den, u.a. f&amp;uuml;r direkte, indirekte, zuf&amp;auml;llige, vorab konkret zu bestimmende oder Folgesch&amp;auml;den, die angeblich durch den oder in Verbindung mit dem Zugang und/oder der Benutzung dieser Website aufgetreten sind.&lt;br /&gt;\r\n&lt;br /&gt;\r\nDer Betreiber &amp;uuml;bernimmt keine Verantwortung f&amp;uuml;r die Inhalte und die Verf&amp;uuml;gbarkeit von Websites Dritter, die &amp;uuml;ber externe Links dieses Informationsangebotes erreicht werden. Der Deutsche Bundesjugendring distanziert sich ausdr&amp;uuml;cklich von allen Inhalten, die m&amp;ouml;glicherweise straf- oder haftungsrechtlich relevant sind oder gegen die guten Sitten versto&amp;szlig;en.&lt;br /&gt;\r\n&lt;br /&gt;\r\nSofern innerhalb des Internetangebotes die M&amp;ouml;glichkeit zur Eingabe pers&amp;ouml;nlicher Daten (z.B. E-Mail-Adressen, Namen) besteht, so erfolgt die Preisgabe dieser Daten seitens der Nutzer_innen auf ausdr&amp;uuml;cklich freiwilliger Basis.&lt;/p&gt;\r\n\r\n&lt;h3&gt;&amp;nbsp;&lt;/h3&gt;\r\n\r\n&lt;h3&gt;&lt;a id=&quot;textnutzung&quot; name=&quot;textnutzung&quot;&gt;&lt;/a&gt;Nutzungsrechte f&amp;uuml;r Texte und Dateien&lt;/h3&gt;\r\n\r\n&lt;p&gt;Soweit nicht anders angegeben liegen alle Rechte beim Deutschen Bundesjugendring. Davon ausgenommen sind Texte und Dateien unter CreativeCommons-Lizenzen.&lt;/p&gt;\r\n\r\n&lt;p&gt;Soweit im Einzelfall nicht anders geregelt und soweit nicht fremde Rechte betroffen sind, ist die Verbreitung der Dokumente und Bilder von&amp;nbsp;&lt;a href=&quot;http://tool.ichmache-politik.de&quot; target=&quot;_blank&quot;&gt;tool.ichmache-politik.de&lt;/a&gt;&amp;nbsp;als Ganzes oder in Teilen davon in elektronischer und gedruckter Form unter der Voraussetzung erw&amp;uuml;nscht, dass die Quelle (&lt;a href=&quot;http://tool.ichmache-politik.de&quot; target=&quot;_blank&quot;&gt;tool.ichmache-politik.de&lt;/a&gt;) genannt wird. Ohne vorherige schriftliche Genehmigung durch den Deutschen Bundesjugendring ist eine kommerzielle Verbreitung der bereitgestellten Texte, Informationen und Bilder ausdr&amp;uuml;cklich untersagt.&lt;/p&gt;\r\n\r\n&lt;p&gt;Nutzungsrechte f&amp;uuml;r Inhalte, die von Nutzer_innen erstellt werden, werden unter einer CreativeCommons-Lizenz zur Verf&amp;uuml;gung gestellt, sofern nicht anders gekennzeichnet.&lt;/p&gt;\r\n\r\n&lt;h4&gt;&amp;nbsp;&lt;/h4&gt;\r\n\r\n&lt;h3&gt;&lt;a id=&quot;datenschutz&quot; name=&quot;datenschutz&quot;&gt;&lt;/a&gt;Datenschutzhinweise&lt;/h3&gt;\r\n\r\n&lt;p&gt;Der Deutsche Bundesjugendring nimmt den Schutz personenbezogener Daten sehr ernst. Wir m&amp;ouml;chten, dass jede_r wei&amp;szlig;, wann wir welche Daten erheben und wie wir sie verwenden. Wir haben technische und organisatorische Ma&amp;szlig;nahmen getroffen, die sicherstellen, dass die Vorschriften &amp;uuml;ber den Datenschutz sowohl von uns als auch von externen Dienstleistenden beachtet werden.&lt;br /&gt;\r\n&lt;br /&gt;\r\nIm Zuge der Weiterentwicklung unserer Webseiten und der Implementierung neuer Technologien, um unseren Service zu verbessern, k&amp;ouml;nnen auch &amp;Auml;nderungen dieser Datenschutzerkl&amp;auml;rung erforderlich werden. Daher empfehlen wir, sich diese Datenschutzerkl&amp;auml;rung ab und zu erneut durchzulesen.&lt;/p&gt;\r\n\r\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;h3&gt;&lt;a id=&quot;zugriff www&quot; name=&quot;zugriff www&quot;&gt;&lt;/a&gt;Zugriff auf das Internetangebot&lt;/h3&gt;\r\n\r\n&lt;p&gt;Jeder Zugriff auf das Internetangebot&amp;nbsp;&lt;a href=&quot;http://tool.ichmache-politik.de&quot; target=&quot;_blank&quot;&gt;tool.ichmache-politik.de&lt;/a&gt;&amp;nbsp;wird in einer Protokolldatei gespeichert. In der Protokolldatei werden folgende Daten gespeichert:&lt;/p&gt;\r\n\r\n&lt;ul&gt;\r\n	&lt;li&gt;Informationen &amp;uuml;ber die Seite, von der aus die Datei angefordert wurde&lt;/li&gt;\r\n	&lt;li&gt;Name der abgerufenen Datei&lt;/li&gt;\r\n	&lt;li&gt;Datum und Uhrzeit des Abrufs&lt;/li&gt;\r\n	&lt;li&gt;&amp;uuml;bertragene Datenmenge&lt;/li&gt;\r\n	&lt;li&gt;Meldung, ob der Abruf erfolgreich war&lt;/li&gt;\r\n&lt;/ul&gt;\r\n\r\n&lt;p&gt;Die gespeicherten Daten werden ausschlie&amp;szlig;lich zur Optimierung des Internetangebotes ausgewertet. Die Angaben speichern wir auf Servern in Deutschland. Der Zugriff darauf ist nur wenigen, besonders befugten Personen m&amp;ouml;glich, die mit der technischen, kaufm&amp;auml;nnischen oder redaktionellen Betreuung der Server befasst sind.&lt;/p&gt;\r\n\r\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;h3&gt;&lt;a id=&quot;datenweitergabe&quot; name=&quot;datenweitergabe&quot;&gt;&lt;/a&gt;Weitergabe personenbezogener Daten an Dritte&lt;/h3&gt;\r\n\r\n&lt;p&gt;Daten, die beim Zugriff auf das Internetangebot&amp;nbsp;&lt;a href=&quot;http://tool.ichmache-politik.de&quot; target=&quot;_blank&quot;&gt;tool.ichmache-politik.de&lt;/a&gt;&amp;nbsp;protokolliert worden sind, werden an Dritte nur &amp;uuml;bermittelt, soweit wir gesetzlich oder durch Gerichtsentscheidung dazu verpflichtet sind oder die Weitergabe im Falle von Angriffen auf die Internetinfrastruktur zur Rechts- oder Strafverfolgung erforderlich ist. Eine Weitergabe zu anderen nichtkommerziellen oder zu kommerziellen Zwecken erfolgt nicht.&lt;/p&gt;\r\n\r\n&lt;p&gt;Eingegebene personenbezogene Informationen werden vom Deutschen Bundesjugendring ausschlie&amp;szlig;lich zur Kommunikation mit den Nutzer_ innen verwendet. Wir geben sie ausdr&amp;uuml;cklich nicht an Dritte weiter.&lt;/p&gt;\r\n\r\n&lt;h3&gt;&lt;br /&gt;\r\n&lt;a id=&quot;u18&quot; name=&quot;u18&quot;&gt;&lt;/a&gt;Schutz von Minderj&amp;auml;hrigen&lt;/h3&gt;\r\n\r\n&lt;p&gt;Personen unter 18 Jahren sollten ohne Zustimmung der Eltern oder Erziehungsberechtigten keine personenbezogenen Daten an uns &amp;uuml;bermitteln. Wir fordern keine personenbezogenen Daten von Kindern und Jugendlichen an. Wissentlich sammeln wir solche Daten nicht und geben sie auch nicht an Dritte weiter.&lt;/p&gt;\r\n\r\n&lt;p&gt;F&amp;uuml;r weitere Informationen in Bezug auf die Behandlung von personenbezogenen Daten steht zur Verf&amp;uuml;gung:&lt;/p&gt;\r\n\r\n&lt;p&gt;Michael Scholl&lt;/p&gt;\r\n\r\n&lt;p&gt;Telefon: +49 (0)30.400 40-412&lt;br /&gt;\r\nTelefax: +49 (0)30.400 40-422&lt;/p&gt;\r\n\r\n&lt;p&gt;E-Mail:&amp;nbsp;&lt;a href=&quot;mailto:info@dbjr.de&quot;&gt;info@dbjr.de&lt;/a&gt;&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;a href=&quot;http://tool.ichmache-politik.de/privacy&quot; target=&quot;_blank&quot;&gt;&amp;raquo; Weitere Informationen zum Datenschutz&lt;/a&gt;&lt;/p&gt;\r\n\r\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;h3&gt;&lt;a id=&quot;design&quot; name=&quot;design&quot;&gt;&lt;/a&gt;Gestaltung&lt;/h3&gt;\r\n\r\n&lt;p&gt;&lt;a href=&quot;http://www.die-projektoren.de&quot; target=&quot;_blank&quot;&gt;DIE.PROJEKTOREN &amp;ndash; FARYS &amp;amp; RUSCH GBR&lt;/a&gt;&amp;nbsp;&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;h3&gt;&lt;a id=&quot;progammierung&quot; name=&quot;progammierung&quot;&gt;&lt;/a&gt;Programmierung&lt;/h3&gt;\r\n\r\n&lt;ul&gt;\r\n	&lt;li&gt;Anne Bohnet&lt;/li&gt;\r\n	&lt;li&gt;&lt;a href=&quot;http://www.digitalroyal.de&quot; target=&quot;_blank&quot;&gt;Digital Royal GmbH&amp;nbsp;&lt;/a&gt;&lt;/li&gt;\r\n	&lt;li&gt;Tim Schrock&lt;/li&gt;\r\n	&lt;li&gt;&lt;a href=&quot;http://www.seitenmeister.com&quot; target=&quot;_blank&quot;&gt;seitenmeister&lt;/a&gt;&amp;nbsp;&lt;/li&gt;\r\n	&lt;li&gt;Synerigc&lt;/li&gt;\r\n	&lt;li&gt;&lt;a href=&quot;http://www.xima.de&quot; target=&quot;_blank&quot;&gt;xima media GmbH&amp;nbsp;&lt;/a&gt;&lt;/li&gt;\r\n&lt;/ul&gt;\r\n\r\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;h3&gt;&lt;a id=&quot;software&quot; name=&quot;software&quot;&gt;&lt;/a&gt;Verwendete Software&lt;/h3&gt;\r\n\r\n&lt;p&gt;Das Internetangebot&amp;nbsp;&lt;sup&gt;e&lt;/sup&gt;Partool basiert auf quelloffener Software. Wir verwenden u.a. den&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;ul&gt;\r\n	&lt;li&gt;&lt;a href=&quot;https://httpd.apache.org/&quot; target=&quot;_blank&quot;&gt;Apache Webserver&lt;/a&gt;&amp;nbsp;mit&amp;nbsp;&lt;a href=&quot;http://php.net/&quot; target=&quot;_blank&quot;&gt;PHP&lt;/a&gt;&amp;nbsp;und&amp;nbsp;&lt;a href=&quot;http://mysql.com/&quot; target=&quot;_blank&quot;&gt;MySQL-Datenbanken&lt;/a&gt;&lt;/li&gt;\r\n	&lt;li&gt;&lt;a href=&quot;http://framework.zend.com/&quot; target=&quot;_blank&quot;&gt;Zend PHP Framework&lt;/a&gt;&lt;/li&gt;\r\n	&lt;li&gt;&lt;a href=&quot;http://twitter.github.io/bootstrap/&quot; target=&quot;_blank&quot;&gt;Bootstrap&lt;/a&gt;&lt;/li&gt;\r\n	&lt;li&gt;&lt;a href=&quot;http://ckeditor.com/&quot; target=&quot;_blank&quot;&gt;CKEditor&lt;/a&gt;&lt;/li&gt;\r\n	&lt;li&gt;&lt;a href=&quot;http://www.yaml.de/&quot; target=&quot;_blank&quot;&gt;YAML&lt;/a&gt;&lt;/li&gt;\r\n&lt;/ul&gt;\r\n\r\n&lt;p&gt;Berlin im Mai 2013&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;a href=&quot;#oben&quot;&gt;&amp;nbsp;Nach oben&lt;/a&gt;&lt;/p&gt;\r\n', '&lt;h4&gt;&lt;a href=&quot;#kontakt&quot;&gt;Kontakt&lt;/a&gt;&lt;/h4&gt;\r\n\r\n&lt;h4&gt;&lt;a href=&quot;#impressum&quot;&gt;Impressum&lt;/a&gt;&lt;/h4&gt;\r\n\r\n&lt;h4&gt;&lt;a href=&quot;#gap&quot;&gt;Nutzung des Gender_Gap&lt;/a&gt;&lt;/h4&gt;\r\n\r\n&lt;h4&gt;&lt;a href=&quot;#Rechtliches&quot;&gt;Rechtliche Hinweise&lt;/a&gt;&lt;/h4&gt;\r\n\r\n&lt;p&gt;&lt;a href=&quot;#textnutzung&quot;&gt;Nutzungsrechte f&amp;uuml;r Texte und Dateien&lt;/a&gt;&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;a href=&quot;#datenschutz&quot;&gt;Datenschutzhinweise&lt;/a&gt;&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;a href=&quot;#zugriff www&quot;&gt;Zugriff auf das Internetangebot&lt;/a&gt;&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;a href=&quot;#datenweitergabe&quot;&gt;Weitergabe personenbezogener Daten an Dritte&lt;/a&gt;&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;a href=&quot;#u18&quot;&gt;Schutz von Minderj&amp;auml;hrigen&lt;/a&gt;&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;a href=&quot;#design&quot;&gt;Gestaltung&lt;/a&gt;&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;a href=&quot;#progammierung&quot;&gt;Programmierung&lt;/a&gt;&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;a href=&quot;#software&quot;&gt;Verwendete Software&lt;/a&gt;&lt;/p&gt;\r\n', 0),
(20, 0, 'xx', 'Häufige Fragen', 'n', 'faq', '&lt;h1&gt;H&amp;auml;ufig gestellte Fragen&lt;/h1&gt;\r\n\r\n&lt;p&gt;&lt;span style=&quot;color:#008080;&quot;&gt;H&amp;auml;ufig kommen Fragen rund um die Beteiligungsrunden und das&lt;sup&gt; e&lt;/sup&gt;Partool auf. Auf dieser Seite k&amp;ouml;nnt ihr einige bereits im Voraus beantworten. Hier findet ihr eine Auswahl potentieller Fragen und teilweise auch Antworten, die ihr nach eurern W&amp;uuml;nschen erg&amp;auml;nzen und anpassen k&amp;ouml;nnt.&lt;/span&gt;&lt;/p&gt;\r\n\r\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;ol&gt;\r\n	&lt;li&gt;\r\n	&lt;h2&gt;&lt;a id=&quot;Worum&quot; name=&quot;Worum&quot;&gt;&lt;/a&gt;Worum geht es hier eigentlich?&lt;/h2&gt;\r\n	&lt;/li&gt;\r\n&lt;/ol&gt;\r\n\r\n&lt;p style=&quot;margin-left: 40px; &quot;&gt;&lt;span style=&quot;color:#008080;&quot;&gt;Hier kommt eine kurzbeschreibung eures Projekts hin. Unsere lautete so:&lt;/span&gt;&lt;/p&gt;\r\n\r\n&lt;p style=&quot;margin-left: 40px; &quot;&gt;Das Bundesministerium f&amp;uuml;r Familie, Senioren, Frauen und Jugend (BMFSFJ) hat 2011 einen Prozess zur Entwicklung einer Eigenst&amp;auml;ndigen Jugendpolitik (EiJP) gestartet. Ein solcher Prozess ist undenkbar ohne die Beteiligung junger Menschen &amp;ndash; also undenkbar ohne EUCH! Darum wird die Jugendbeteiligung am Prozess &amp;uuml;ber Ichmache&amp;gt;Politik initiiert und abgesichert. Das ist ein Projekt des Deutschen Bundesjugendrings (DBJR).&lt;br /&gt;\r\n&lt;br /&gt;\r\nIchmache&amp;gt;Politik erm&amp;ouml;glicht es jungen Menschen zwischen 12 und 27 Jahren in unterschiedlichen Kontexten (Gruppe, Verband, Schule, etc.) oder als Einzelpersonen, sich vor Ort mit den Themen und Ergebnissen des EiJP-Prozesses auseinanderzusetzen sowie diese online &amp;uuml;ber unser ePartool zu bewerten und zu qualifizieren. &amp;Uuml;ber das ePartool werden eure Beitr&amp;auml;ge gesammelt und sp&amp;auml;ter von allen Teilnehmenden gewichtet. Die Resultate gehen schlie&amp;szlig;lich in die Entscheidungsfindung des EiJP-Prozesses ein: Politische Akteur_innen besch&amp;auml;ftigen sich bewusst und ernsthaft mit den Ergebnissen der Jugendbeteiligung und geben euch schlie&amp;szlig;lich ein Feedback &amp;uuml;ber die Wirkung Eures Engagements. Junge Menschen &amp;ndash; also ihr &amp;ndash; wirken somit an der Entwicklung einer Eigenst&amp;auml;ndigen Jugendpolitik mit. Wichtig ist hierbei, dass ihr nicht nur Impulsgeber_innen sein sollt, sondern vor allem Beurteilungsinstanz f&amp;uuml;r die inhaltlichen Ergebnisse im Prozessverlauf seid.&lt;/p&gt;\r\n\r\n&lt;p style=&quot;margin-left: 40px; &quot;&gt;Mehr zum Projekt und zum Prozess erfahrt ihr unter &amp;gt;&amp;gt; &lt;a href=&quot;/about#what&quot; target=&quot;_blank&quot;&gt;WAS WIR MIT EUCH MACHEN.&lt;/a&gt;&lt;/p&gt;\r\n\r\n&lt;p style=&quot;margin-left: 40px; &quot;&gt;&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;ol&gt;\r\n	&lt;li value=&quot;2&quot;&gt;\r\n	&lt;h2&gt;&lt;a name=&quot;Wer kann sich&quot;&gt;&lt;/a&gt;Wer kann sich hier beteiligen?&lt;/h2&gt;\r\n	&lt;/li&gt;\r\n&lt;/ol&gt;\r\n\r\n&lt;p style=&quot;margin-left: 40px; &quot;&gt;&lt;span style=&quot;color:#008080;&quot;&gt;Na, wer denn?&lt;/span&gt;&lt;/p&gt;\r\n\r\n&lt;ol start=&quot;3&quot;&gt;\r\n	&lt;li&gt;\r\n	&lt;h2&gt;&lt;a name=&quot;Worauf sollte ich&quot;&gt;&lt;/a&gt;Worauf sollte ich beim Eintragen der Beitr&amp;auml;ge achten?&lt;/h2&gt;\r\n	&lt;/li&gt;\r\n&lt;/ol&gt;\r\n\r\n&lt;p style=&quot;margin-left: 40px; &quot;&gt;Bitte formuliert eure Beitr&amp;auml;ge m&amp;ouml;glichst knapp und beschr&amp;auml;nkt euch pro Box auf eine Idee bzw. einen Gedanken. Das Eingabefeld f&amp;uuml;r eure Beitr&amp;auml;ge ist begrentzt auf max. 300 Buchstaben. F&amp;uuml;r Erkl&amp;auml;rungen, weitergehende Infos usw. nutzt bitte die jeweilige Erl&amp;auml;uterungsbox.&lt;/p&gt;\r\n\r\n&lt;p style=&quot;margin-left: 40px; &quot;&gt;&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;ol start=&quot;4&quot;&gt;\r\n	&lt;li&gt;\r\n	&lt;h2&gt;&lt;a name=&quot;M&uuml;ssen alle&quot;&gt;&lt;/a&gt;M&amp;uuml;ssen alle Fragen beantwortet werden?&lt;/h2&gt;\r\n	&lt;/li&gt;\r\n&lt;/ol&gt;\r\n\r\n&lt;p style=&quot;margin-left: 40px; &quot;&gt;Nein, ihr k&amp;ouml;nnt frei entscheiden, ob ihr eine, zwei, drei oder alle Fragen beantworten m&amp;ouml;chtet.&lt;br /&gt;\r\n&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;ol start=&quot;5&quot;&gt;\r\n	&lt;li&gt;\r\n	&lt;h2&gt;&lt;a name=&quot;M&uuml;ssen die&quot;&gt;&lt;/a&gt;M&amp;uuml;ssen die Fragen der Reihenfolge nach beantwortet werden?&lt;/h2&gt;\r\n	&lt;/li&gt;\r\n&lt;/ol&gt;\r\n\r\n&lt;p style=&quot;margin-left: 40px; &quot;&gt;Nein, ihr k&amp;ouml;nnt die Reihenfolge, in der ihr die Fragen beantwortet, frei w&amp;auml;hlen und dabei ganz einfach zwischen den Fragen hin und her wechseln.&lt;br /&gt;\r\n&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;ol start=&quot;6&quot;&gt;\r\n	&lt;li&gt;\r\n	&lt;h2&gt;&lt;a name=&quot;Wie kann ich&quot;&gt;&lt;/a&gt;Wie kann ich einen Eintrag von mir l&amp;ouml;schen?&lt;/h2&gt;\r\n	&lt;/li&gt;\r\n&lt;/ol&gt;\r\n\r\n&lt;p style=&quot;margin-left: 40px; &quot;&gt;Ihr k&amp;ouml;nnt einen Eintrag l&amp;ouml;schen, indem ihr den Text in der entsprechenden Box l&amp;ouml;scht.&lt;br /&gt;\r\n&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;ol start=&quot;7&quot;&gt;\r\n	&lt;li&gt;\r\n	&lt;h2&gt;&lt;a name=&quot;Warum muss ich&quot;&gt;&lt;/a&gt;Warum muss ich eine E-Mail-Adresse angeben?&lt;/h2&gt;\r\n	&lt;/li&gt;\r\n&lt;/ol&gt;\r\n\r\n&lt;p style=&quot;margin-left: 40px; &quot;&gt;Die E-Mail-Adresse ist notwendig, damit wir sicherstellen k&amp;ouml;nnen, dass die Eintr&amp;auml;ge von einer realen Person stammen und nicht von einem Spamversender. An die von euch angegebene E-Mail-Adresse schicken wir automatisch eine E-Mail mit einem Best&amp;auml;tigungslink, den ihr aktivieren m&amp;uuml;sst, indem ihr darauf klickt oder ihn in euren Internetbrowser kopiert. Erst dann werden eure Beitr&amp;auml;ge endg&amp;uuml;ltig gespeichert und auf der Website ver&amp;ouml;ffentlicht.&lt;br /&gt;\r\nMit der E-Mail erhaltet ihr gleichzeitig ein Passwort. Dieses ben&amp;ouml;tigt ihr, wenn ihr zu einem sp&amp;auml;teren Zeitpunkt Eintr&amp;auml;ge erg&amp;auml;nzen oder bearbeiten m&amp;ouml;chtet. Ihr solltet unsere E-Mail also f&amp;uuml;r einige Tage aufbewahren!&lt;br /&gt;\r\nWir sichern zu, dass E-Mail-Adressen weder an Dritte weitergegeben noch f&amp;uuml;r andere Zwecke als f&amp;uuml;r diese Jugendbeteiligung genutzt werden.&lt;br /&gt;\r\nWeitere Informationen zum Datenschutz:&amp;nbsp;&lt;a href=&quot;/privacy&quot; target=&quot;_blank&quot;&gt;&amp;gt;&amp;gt; hier&lt;/a&gt;.&lt;br /&gt;\r\n&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;ol start=&quot;8&quot;&gt;\r\n	&lt;li&gt;\r\n	&lt;h2&gt;&lt;a name=&quot;Welche Daten&quot;&gt;&lt;/a&gt;Welche Daten werden gesammelt und wie werden sie weiterverwendet?&lt;/h2&gt;\r\n	&lt;/li&gt;\r\n&lt;/ol&gt;\r\n\r\n&lt;p style=&quot;margin-left: 40px; &quot;&gt;Ausf&amp;uuml;hrliche Infos zum Datenschutz findet ihr&amp;nbsp;&lt;a href=&quot;/privacy&quot; target=&quot;_blank&quot;&gt;&amp;gt;&amp;gt; hier&lt;/a&gt;.&lt;/p&gt;\r\n\r\n&lt;p style=&quot;margin-left: 40px; &quot;&gt;&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;ol start=&quot;9&quot;&gt;\r\n	&lt;li&gt;\r\n	&lt;h2&gt;&lt;a name=&quot;Was passiert&quot;&gt;&lt;/a&gt;Was passiert mit meinen Beitr&amp;auml;gen, nachdem ich auf den Best&amp;auml;tigungslink geklickt habe?&lt;/h2&gt;\r\n	&lt;/li&gt;\r\n&lt;/ol&gt;\r\n\r\n&lt;p style=&quot;margin-left: 40px; &quot;&gt;Das Projektb&amp;uuml;ro &amp;uuml;berpr&amp;uuml;ft alle Eintr&amp;auml;ge und beh&amp;auml;lt sich vor, diese wenn n&amp;ouml;tig zu sperren &amp;ndash; z.B. wenn sie diskriminierende Inhalte haben. Alle gepr&amp;uuml;ften Beitr&amp;auml;ge werden auf tool.ichmache-politik.de ver&amp;ouml;ffentlicht und k&amp;ouml;nnen von anderen Besucher_innen gelesen werden. Euer Name oder eure E-Mail-Adresse sind dabei nicht sichtbar.&lt;br /&gt;\r\n&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;ol start=&quot;10&quot;&gt;\r\n	&lt;li&gt;\r\n	&lt;h2&gt;&lt;a name=&quot;Wie kann ich sehen&quot;&gt;&lt;/a&gt;Wie kann ich sehen, was andere eingetragen haben?&lt;/h2&gt;\r\n	&lt;/li&gt;\r\n&lt;/ol&gt;\r\n\r\n&lt;p style=&quot;margin-left: 40px; &quot;&gt;Ihr k&amp;ouml;nnt euch die Beitr&amp;auml;ge der anderen zu den jeweiligen Fragen ansehen, indem ihr auf der Startseite auf die Box &amp;bdquo;Beitr&amp;auml;ge&amp;ldquo; in der jeweiligen Beteiligungsrunde klickt.&lt;/p&gt;\r\n\r\n&lt;ol start=&quot;11&quot;&gt;\r\n	&lt;li&gt;\r\n	&lt;h2&gt;&lt;br /&gt;\r\n	&lt;a name=&quot;Was muss ich tun&quot;&gt;&lt;/a&gt;Was muss ich tun, um &amp;uuml;ber die Ergebnisse der Beteiligung informiert zu werden?&lt;/h2&gt;\r\n	&lt;/li&gt;\r\n&lt;/ol&gt;\r\n\r\n&lt;p style=&quot;margin-left: 40px; &quot;&gt;Am Ende des Online-Fragebogens k&amp;ouml;nnt ihr angeben, dass ihr &amp;uuml;ber die Ergebnisse der Beteiligung informiert werden m&amp;ouml;chtet. Die Informationen schicken wir dann an die von euch angegebene E-Mail-Adresse. Solltet ihr diesen Service nicht mehr w&amp;uuml;nschen, k&amp;ouml;nnt ihr ihn jederzeit abbestellen. Dar&amp;uuml;ber hinaus k&amp;ouml;nnt ihr&lt;br /&gt;\r\n&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;ol start=&quot;12&quot;&gt;\r\n	&lt;li&gt;\r\n	&lt;h2&gt;&lt;a name=&quot;Mein Passwort&quot;&gt;&lt;/a&gt;Mein Passwort bzw. mein Zugangslink ist verloren gegangen oder funktioniert nicht mehr. Was kann ich tun?&lt;/h2&gt;\r\n	&lt;/li&gt;\r\n&lt;/ol&gt;\r\n\r\n&lt;p style=&quot;margin-left: 40px; &quot;&gt;Klickt im oberen rechten Teil der Website auf &amp;bdquo;Login&amp;ldquo;. Dort k&amp;ouml;nnt ihr ein neues Passwort oder einen neuen Zugangslink anfordern, indem ihr auf &amp;quot;Passwort vergessen&amp;quot; klickt.&lt;br /&gt;\r\n&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;ol start=&quot;13&quot;&gt;\r\n	&lt;li&gt;\r\n	&lt;h2&gt;&lt;a name=&quot;Weshalb&quot;&gt;&lt;/a&gt;Weshalb sieht die Website in verschiedenen Internetbrowsern unterschiedlich aus?&lt;/h2&gt;\r\n	&lt;/li&gt;\r\n&lt;/ol&gt;\r\n\r\n&lt;p style=&quot;margin-left: 40px; &quot;&gt;Wer verschiedene Internetbrowser verwendet, dem werden beim Design der Website Unterschiede auffallen. Das liegt daran, dass unterschiedliche Browserversionen unterschiedliche Anforderungen an die Programmierung stellen. Die Unterschiede haben aber keine Auswirkungen auf die Funktionen der Website. Wir arbeiten daran, die Design-Unterschiede so gering wie m&amp;ouml;glich zu halten. Vorerst empfehlen wir euch, m&amp;ouml;glichst aktuelle Versionen von Firefox, Chrome, Opera oder Safari zu verwenden.&lt;br /&gt;\r\n&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;ol start=&quot;14&quot;&gt;\r\n	&lt;li&gt;\r\n	&lt;h2&gt;&lt;strong&gt;&lt;a name=&quot;Warum findet sich&quot;&gt;&lt;/a&gt;&lt;/strong&gt;Warum findet sich mein Beitrag im Voting nicht im genauen Wortlaut wieder?&lt;/h2&gt;\r\n\r\n	&lt;p&gt;&lt;span style=&quot;color:#008080;&quot;&gt;So machen wir, von Ichmache&amp;gt;Politik das. Ihr auch?&lt;/span&gt;&lt;br /&gt;\r\n	&lt;br /&gt;\r\n	Bevor die Abstimmung gestartet wird, geht die Redaktion alle Beitr&amp;auml;ge noch einmal durch. Drei Punkte sind hierbei wichtig:&lt;/p&gt;\r\n	&lt;/li&gt;\r\n&lt;/ol&gt;\r\n\r\n&lt;ul&gt;\r\n	&lt;li&gt;&lt;strong&gt;Gibt es Beitr&amp;auml;ge mit demselben Inhalt, derselben Aussage, Forderung oder Idee?&lt;/strong&gt; Wenn ja, fassen wir die Beitr&amp;auml;ge, die einen gleichen oder &amp;auml;hnlichen Inhalt haben, zusammen, damit ihr nicht immer wieder &amp;uuml;ber &amp;Auml;hnliches abstimmen m&amp;uuml;sst. Dabei wird immer festgehalten, welche Beitr&amp;auml;ge zusammengeflossen sind oder auch wo genau Teilaspekte gelandet sind.&lt;br /&gt;\r\n	&amp;nbsp;&lt;/li&gt;\r\n	&lt;li&gt;&lt;strong&gt;Enth&amp;auml;lt ein Beitrag mehrere unterschiedliche Aussagen, Forderungen oder Ideen? &lt;/strong&gt;Wenn ja, &amp;quot;splitten&amp;quot; wir den Beitrag &amp;uuml;berlicherweise auf, damit die anderen besser &amp;uuml;ber die einzelnen Aspekte abstimmen k&amp;ouml;nnen.&lt;br /&gt;\r\n	&amp;nbsp;&lt;/li&gt;\r\n	&lt;li&gt;&lt;strong&gt;Sind die Beitr&amp;auml;ge f&amp;uuml;r jeden verst&amp;auml;ndlich formuliert? &lt;/strong&gt;Wenn nicht, achten wir darauf, dass z.B. in euren Beitr&amp;auml;gen verwendete Fremdw&amp;ouml;rter &amp;uuml;bersetzt werden und der Satzbau nicht zu verschachtelt ist, damit die Aussage im Vordergrund steht und f&amp;uuml;r jede_n nachvollziehbar ist.&lt;strong&gt;&amp;nbsp;&lt;/strong&gt;&lt;/li&gt;\r\n&lt;/ul&gt;\r\n\r\n&lt;p style=&quot;margin-left: 40px;&quot;&gt;Wir bem&amp;uuml;hen uns bei der redaktionellen Arbeit darum, so nah wie m&amp;ouml;glich, an euren Formulierungen zu bleiben und inhaltlich nichts zu ver&amp;auml;ndern.&lt;/p&gt;\r\n\r\n&lt;p style=&quot;margin-left: 40px;&quot;&gt;Wenn ihr genauere Ausk&amp;uuml;nfte dazu haben wollt, was mit eurem Beitrag passiert ist, ruft uns einfach an (030 400 40 441). Zuk&amp;uuml;nftig soll das im &lt;sup&gt;e&lt;/sup&gt;Partool sichtbar gemacht werden. Dieses wird stetig weiterentwickelt, der gro&amp;szlig;e Relaunch steht vor der T&amp;uuml;r.&lt;br /&gt;\r\n&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;ol start=&quot;15&quot;&gt;\r\n	&lt;li&gt;\r\n	&lt;h2&gt;&lt;a name=&quot;Warum schreibt&quot;&gt;&lt;/a&gt;Warum schreibt ihr &amp;bdquo;jede_r&amp;ldquo; oder &amp;bdquo;Besucher_innen&amp;ldquo;?&lt;/h2&gt;\r\n	&lt;/li&gt;\r\n&lt;/ol&gt;\r\n\r\n&lt;p style=&quot;margin-left: 40px; &quot;&gt;Das &amp;bdquo;_&amp;ldquo; ist der sogenannte Gender_Gap. Wie verwenden diese Schreibweise um deutlich zu machen, dass wir alle Menschen ansprechen m&amp;ouml;chten unabh&amp;auml;ngig von ihrer Geschlechtsidentit&amp;auml;t. Ein Gender_Gap wird eingef&amp;uuml;gt, um neben dem biologischen das soziale Geschlecht (Gender) darzustellen. Es ist eine aus dem Bereich der Queer-Theorie stammende Variante des Binnen-I. Der Gender_Gap soll ein Mittel der sprachlichen Darstellung aller sozialen Geschlechter und Geschlechtsidentit&amp;auml;ten abseits der Zweigeschlechtlichkeit sein. In der deutschen Sprache w&amp;auml;re dies sonst nur durch Umschreibungen m&amp;ouml;glich. Die Intention ist, durch den Zwischenraum einen Hinweis auf diejenigen Menschen zu geben, die nicht in das ausschlie&amp;szlig;liche Frau-Mann-Schema hineinpassen oder nicht hineinpassen wollen, wie Intersexuelle oder Transgender.&lt;/p&gt;\r\n\r\n&lt;p style=&quot;margin-left: 40px; &quot;&gt;&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;h2&gt;&lt;a name=&quot;Ihr findet&quot;&gt;&lt;/a&gt;Ihr findet hier keine Antwort auf eure Frage?&lt;/h2&gt;\r\n\r\n&lt;p style=&quot;margin-left: 40px; &quot;&gt;Dann wendet euch an das PROJEKTNAME Projektb&amp;uuml;ro&lt;br /&gt;\r\nE-Mail:&lt;br /&gt;\r\nTelefon:&lt;/p&gt;\r\n', '&lt;ol&gt;\r\n	&lt;li&gt;\r\n	&lt;p&gt;&lt;a href=&quot;#Worum&quot;&gt;Worum geht es hier eigentlich?&lt;/a&gt;&lt;/p&gt;\r\n	&lt;/li&gt;\r\n	&lt;li&gt;\r\n	&lt;p&gt;&lt;a href=&quot;#Wer kann sich&quot;&gt;Wer kann sich hier beteiligen?&lt;/a&gt;&lt;/p&gt;\r\n	&lt;/li&gt;\r\n	&lt;li&gt;\r\n	&lt;p&gt;&lt;a href=&quot;#Worauf sollte ich&quot;&gt;Worauf sollte ich beim Eintragen der Beitr&amp;auml;ge achten?&lt;/a&gt;&lt;/p&gt;\r\n	&lt;/li&gt;\r\n	&lt;li&gt;\r\n	&lt;p&gt;&lt;a href=&quot;#M&uuml;ssen alle&quot;&gt;M&amp;uuml;ssen alle Fragen beantwortet werden?&lt;/a&gt;&lt;/p&gt;\r\n	&lt;/li&gt;\r\n	&lt;li&gt;\r\n	&lt;p&gt;&lt;a href=&quot;#M&uuml;ssen die&quot;&gt;M&amp;uuml;ssen die Fragen der Reihenfolge nach beantwortet werden?&lt;/a&gt;&lt;/p&gt;\r\n	&lt;/li&gt;\r\n	&lt;li&gt;\r\n	&lt;p&gt;&lt;a href=&quot;#Wie kann ich&quot;&gt;Wie kann ich einen Eintrag von mir l&amp;ouml;schen?&lt;/a&gt;&lt;/p&gt;\r\n	&lt;/li&gt;\r\n	&lt;li&gt;\r\n	&lt;p&gt;&lt;a href=&quot;#Warum muss ich&quot;&gt;Warum muss ich eine E-Mail-Adresse angeben?&lt;/a&gt;&lt;/p&gt;\r\n	&lt;/li&gt;\r\n	&lt;li&gt;\r\n	&lt;p&gt;&lt;a href=&quot;#Welche Daten&quot;&gt;Welche Daten werden gesammelt und wie werden sie weiterverwendet?&lt;/a&gt;&lt;/p&gt;\r\n	&lt;/li&gt;\r\n	&lt;li&gt;\r\n	&lt;p&gt;&lt;a href=&quot;#Was passiert&quot;&gt;Was passiert mit meinen Beitr&amp;auml;gen, nachdem ich auf den Best&amp;auml;tigungslink geklickt habe?&lt;/a&gt;&lt;/p&gt;\r\n	&lt;/li&gt;\r\n	&lt;li&gt;\r\n	&lt;p&gt;&lt;a href=&quot;#Wie kann ich sehen&quot;&gt;Wie kann ich sehen, was andere eingetragen haben?&lt;/a&gt;&lt;/p&gt;\r\n	&lt;/li&gt;\r\n	&lt;li&gt;\r\n	&lt;p&gt;&lt;a href=&quot;#Was muss ich tun&quot;&gt;Was muss ich tun, um &amp;uuml;ber die Ergebnisse der Beteiligung informiert zu werden?&lt;/a&gt;&lt;/p&gt;\r\n	&lt;/li&gt;\r\n	&lt;li&gt;\r\n	&lt;p&gt;&lt;a href=&quot;#Worum&quot;&gt;Mein Passwort bzw. mein Zugangslink ist verloren gegangen oder funktioniert nicht mehr. Was kann ich tun?&lt;/a&gt;&lt;/p&gt;\r\n	&lt;/li&gt;\r\n	&lt;li&gt;\r\n	&lt;p&gt;&lt;a href=&quot;#Weshalb&quot;&gt;Weshalb sieht die Website in verschiedenen Internetbrowsern unterschiedlich aus?&lt;/a&gt;&lt;/p&gt;\r\n	&lt;/li&gt;\r\n	&lt;li&gt;\r\n	&lt;p&gt;&lt;a href=&quot;#Warum findet sich&quot;&gt;Warum findet sich mein Beitrag im Voting nicht im genauen Wortlaut wieder?&lt;/a&gt;&lt;/p&gt;\r\n	&lt;/li&gt;\r\n	&lt;li&gt;\r\n	&lt;p&gt;&lt;a href=&quot;#Warum schreibt&quot;&gt;Warum schreibt ihr &amp;bdquo;jede_r&amp;ldquo; oder &amp;bdquo;Besucher_innen&amp;ldquo;?&lt;/a&gt;&lt;/p&gt;\r\n	&lt;/li&gt;\r\n&lt;/ol&gt;\r\n\r\n&lt;p&gt;&lt;a href=&quot;#Ihr findet&quot;&gt;Ihr findet hier keine Antwort auf eure Frage?&lt;/a&gt;&lt;/p&gt;\r\n', 0),
(22, 0, 'xx', 'Erklärungstext Abstimmungsergebnisse', 'y', '0', '&lt;p&gt;&lt;span style=&quot;color:#008080;&quot;&gt;Das ist der Text der &amp;uuml;ber der Liste mit den Abstimmungsergebnissen steht, nachdem der Abstimmungszeitraum abgelaufen ist. So in etwa k&amp;ouml;nnte er formuliert sein:&lt;/span&gt;&lt;/p&gt;\r\n\r\n&lt;p&gt;Hier seht ihr die Ergebnisse dieser Beteiligungsrunde. Die Beitr&amp;auml;ge, die die Teilnehmenden am wichtigsten fanden, stehen in der Liste ganz oben: Je l&amp;auml;nger der Balken, desto h&amp;ouml;her die Bewertung.&lt;/p&gt;\r\n\r\n&lt;p&gt;Im Men&amp;uuml; links k&amp;ouml;nnt ihr zwischen den verschiedenen Fragestellungen wechseln.&lt;/p&gt;\r\n\r\n&lt;p&gt;Genau wie bei den Beitr&amp;auml;gen konnten auch bei der Abstimmung Einzelpersonen und Gruppen verschiedener Gr&amp;ouml;&amp;szlig;e teilnehmen. Die Berechnung der Abstimmungsergebnisse achtet genau auf verschiedene Details: Dabei wird ber&amp;uuml;cksichtigt, wie viele Personen &amp;uuml;ber den jeweiligen Beitrag abgestimmt haben, wie sie ihn bewerteten und welches Gewicht ihre Stimme hatte. Das Stimmgewicht der Teilnehmenden ist davon abh&amp;auml;ngig, ob sie als Einzelpersonen oder als Gruppenvertreter_innen abgestimmt haben und wie gro&amp;szlig; die Gruppe ist.&lt;/p&gt;\r\n\r\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;\r\n', '', 0),
(97, 0, 'xx', 'Follow-up Einführungstext', 'n', '0', '&lt;p&gt;Welche R&amp;uuml;ckmeldungen haben politische Entscheidungstr&amp;auml;ger_innen auf eure Ideen, Vorschl&amp;auml;ge und Positionen bislang gegeben? In welche Dokumente und eventuell auch Entscheidungen finden sich eure Beitr&amp;auml;ge wieder? Die Antworten auf diese Fragen findet Ihr hier!&lt;/p&gt;\r\n\r\n&lt;p&gt;Wir bem&amp;uuml;hen uns darum darzustellen, was nach Abschluss der jeweiligen Beteiligungsrunde passiert ist. So k&amp;ouml;nnt Ihr nachvollziehen, was aus Euren Beitr&amp;auml;gen geworden ist, welche Personen, Institutionen und Gremien sich damit auseinandergesetzt haben, wie ihr Feedback aussah und ob es bereits konkrete Ergebnisse gibt.&lt;/p&gt;\r\n', '', 0),
(133, 0, 'xx', 'Was wir mit euch machen', 'n', 'about', '&lt;p&gt;&lt;em&gt;Hier erfahren die Teilnehmenden mehr dar&amp;uuml;ber, &lt;/em&gt;&lt;a href=&quot;#what&quot;&gt;&lt;strong&gt;&amp;rsaquo;&amp;rsaquo;&amp;rsaquo;WAS&lt;/strong&gt;&lt;/a&gt;&lt;em&gt; es mit eurem Projekt auf sich hat. &amp;ndash; Sie sollen herausfinden, wer &lt;/em&gt;&lt;a href=&quot;#us&quot;&gt;&lt;strong&gt;&amp;rsaquo;&amp;rsaquo;&amp;rsaquo;WIR&lt;/strong&gt;&lt;/a&gt;&lt;em&gt;, also IHR, seid. &amp;ndash; Sie k&amp;ouml;nnen nachlesen wen ihr ansprechen wollt &lt;/em&gt;&lt;a href=&quot;#you&quot;&gt;&lt;strong&gt;&amp;rsaquo;&amp;rsaquo;&amp;rsaquo;MIT EUCH&lt;/strong&gt;&lt;/a&gt;&lt;em&gt; &amp;ndash; Und sie k&amp;ouml;nnen sich kurz und knapp dar&amp;uuml;ber informieren, wie sie mit&lt;/em&gt;&lt;a href=&quot;#vision&quot;&gt;&lt;strong&gt;&amp;rsaquo;&amp;rsaquo;&amp;rsaquo;MACHEN&lt;/strong&gt;&lt;/a&gt;&lt;em&gt; k&amp;ouml;nnen.&lt;/em&gt;&lt;br /&gt;\r\n&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;h1&gt;&lt;a name=&quot;what&quot;&gt;&lt;/a&gt;Was&lt;/h1&gt;\r\n\r\n&lt;h1&gt;&lt;br /&gt;\r\n&lt;br /&gt;\r\n&lt;a name=&quot;us&quot;&gt;&lt;/a&gt; Wir&lt;/h1&gt;\r\n\r\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;h2&gt;&amp;nbsp;&lt;/h2&gt;\r\n\r\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;h1&gt;&lt;a name=&quot;you&quot;&gt;&lt;/a&gt; Mit euch&lt;/h1&gt;\r\n\r\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;h1&gt;&lt;br /&gt;\r\n&lt;a name=&quot;vision&quot;&gt;&lt;/a&gt; Machen&lt;/h1&gt;\r\n\r\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;h2&gt;1. Ideen, Vorschl&amp;auml;ge und Forderungen entwickeln!&lt;/h2&gt;\r\n\r\n&lt;p&gt;Setzt euch vor Ort, in eurer Gruppe oder auch alleine mit den Themen der Beteiligungsrunde auseinander. Ihr entscheidet dabei, wie ihr das genau machen wollt. Ob ihr dazu eine kleine Diskussion im Freundeskreis durchf&amp;uuml;hrt, einen Workshop darauf organisiert oder eine gr&amp;ouml;&amp;szlig;ere Aktion startet, bleibt euch &amp;uuml;berlassen. Ebenso, ob ihr euch alle Fragen vornehmt oder nur ein oder zwei.&lt;/p&gt;\r\n\r\n&lt;p&gt;Findet heraus, wo das Thema in eurer Umgebung &amp;uuml;berall eine Rolle spielt, diskutiert im Verband, in der Schule, mit Freunden oder mit Verantwortlichen und bildet euch eine Meinung. Wir sammeln sowohl Einzelmeinungen als auch Ergebnisse aus Workshops, Gespr&amp;auml;chen am Lagerfeuer oder thematischen Gruppenstunden. Selbstverst&amp;auml;ndlich k&amp;ouml;nnt ihr auch Teile aus fertigen Beschl&amp;uuml;ssen verwenden, z. B. Positionspapiere eures Verbandes oder eurer Initiative.&lt;/p&gt;\r\n\r\n&lt;h2&gt;&lt;br /&gt;\r\n2. Beitragen!&lt;/h2&gt;\r\n\r\n&lt;p&gt;Wenn eure Ideen, Vorschl&amp;auml;ge und Forderungen fertig sind, tragt ihr sie hier online anhand der Fragen ein. Dort k&amp;ouml;nnt ihr auch nachgucken, was andere bereits geschrieben haben. So k&amp;ouml;nnen die Ergebnisse eurer Arbeit weitreichendere Bedeutung bekommen und Jugendpolitik in Deutschland und der EU beeinflussen.&lt;/p&gt;\r\n\r\n&lt;p&gt;Bitte formuliert eure Beitr&amp;auml;ge m&amp;ouml;glichst knapp und beschr&amp;auml;nkt euch pro Box auf eure &amp;bdquo;Kernbotschaft&amp;ldquo; (max. 300 Buchstaben). F&amp;uuml;r Erkl&amp;auml;rungen, weitergehende Infos usw. nutzt bitte die jeweilige Erl&amp;auml;uterungsbox.&lt;/p&gt;\r\n\r\n&lt;h2&gt;&lt;br /&gt;\r\n3. Abstimmen!&lt;/h2&gt;\r\n\r\n&lt;p&gt;Nach dem Ende der Beitragsphase seid ihr ein zweites Mal gefragt! Gemeinsam mit den anderen Teilnehmer_innen der Beteiligungsrunde k&amp;ouml;nnt ihr dar&amp;uuml;ber abstimmen, welche der Beitr&amp;auml;ge eurer Meinung nach besonders wichtig f&amp;uuml;r die weitere politische Diskussion in der EU und hier in Deutschland sind. Wie viele f&amp;uuml;r eure Gruppe an der Abstimmung teilnehmen, ob alle, nur einige oder ein_e Gruppenvertreter_in k&amp;ouml;nnt ihr frei entscheiden.&lt;/p&gt;\r\n\r\n&lt;p&gt;Um euch das Abstimmen zu vereinfachen und die Beitr&amp;auml;ge auf eine abstimmbare Zahl zu reduzieren, fassen wir inhaltlich identische Beitr&amp;auml;ge redaktionell zusammen bzw. unterteilen facettenreiche Positionen in ihre einzelnen Aspekte. Dabei bem&amp;uuml;hen wir uns darum, so nah wie m&amp;ouml;glich am Inhalt eures Beitrags zu bleiben und nichts zu verf&amp;auml;lschen. Ihr k&amp;ouml;nnt dabei nachvollziehen, aus welchen eurer Antworten sich ein zur Abstimmung stehender Beitrag zusammensetzt. &amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;p&gt;Durch die Abstimmung bestimmt ihr dar&amp;uuml;ber, was weiterkommt und was nicht. Die Beitr&amp;auml;ge mit der h&amp;ouml;chsten Punktzahl flie&amp;szlig;en am Ende in die Zusammenfassung ein.&lt;/p&gt;\r\n\r\n&lt;p&gt;&amp;Uuml;brigens: Aus Zeitgr&amp;uuml;nden m&amp;uuml;ssen wir manchmal auf die Abstimmung verzichten. In dem Fall ber&amp;uuml;cksichtigen wir alle Beitr&amp;auml;ge in der Zusammenfassung.&lt;/p&gt;\r\n\r\n&lt;h2&gt;&lt;br /&gt;\r\n4. Wirkung erzielen!&lt;/h2&gt;\r\n\r\n&lt;p&gt;Wir sorgen daf&amp;uuml;r, dass eure Ideen, Vorschl&amp;auml;ge und Forderungen an die Zust&amp;auml;ndigen weitergeleitet werden und damit in die politischen Diskussionen einflie&amp;szlig;en. Einige politische Akteur_innen hier in Deutschland haben verbindlich zugesagt, sich mit den Ergebnissen auseinanderzusetzen und euch eine R&amp;uuml;ckmeldung dazu zu geben. Weitere fragen wir je nach Thema an. Wenn Zwischenergebnisse und Reaktionen vorliegen, informieren wir euch dar&amp;uuml;ber.&lt;/p&gt;\r\n', '', 0);
INSERT INTO `articles` (`art_id`, `kid`, `proj`, `desc`, `hid`, `ref_nm`, `artcl`, `sidebar`, `parent_id`) VALUES
(153, 0, 'xx', 'Impressum', 'n', 'imprint', '&lt;h1&gt;&lt;a id=&quot;oben&quot; name=&quot;oben&quot;&gt;&lt;/a&gt;Impressum&lt;/h1&gt;\r\n\r\n&lt;h2&gt;&lt;br /&gt;\r\nHerausgeber dieser Website&lt;/h2&gt;\r\n\r\n&lt;h3&gt;&lt;br /&gt;\r\nVerantwortlich&lt;/h3&gt;\r\n\r\n&lt;h3&gt;Redaktion&lt;/h3&gt;\r\n\r\n&lt;h3&gt;Adresse&lt;/h3&gt;\r\n\r\n&lt;h3&gt;Kontakt&lt;/h3&gt;\r\n\r\n&lt;p&gt;Telefon:&lt;br /&gt;\r\nTelefax:&lt;br /&gt;\r\nE-Mail:&lt;br /&gt;\r\nInternet:&lt;/p&gt;\r\n\r\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;h2&gt;&lt;a id=&quot;Rechtliches&quot; name=&quot;Rechtliches&quot;&gt;&lt;/a&gt;Rechtliche Hinweise&lt;/h2&gt;\r\n\r\n&lt;p&gt;Alle Angaben unseres Internetangebotes wurden sorgf&amp;auml;ltig gepr&amp;uuml;ft. Wir bem&amp;uuml;hen uns, dieses Informationsangebot aktuell und inhaltlich richtig sowie vollst&amp;auml;ndig anzubieten. Dennoch ist das Auftreten von Fehlern nicht v&amp;ouml;llig auszuschlie&amp;szlig;en. Eine Garantie f&amp;uuml;r die Vollst&amp;auml;ndigkeit, Richtigkeit und letzte Aktualit&amp;auml;t kann daher nicht &amp;uuml;bernommen werden.&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;br /&gt;\r\nDer Deutsche Bundesjugendring kann diese Website nach eigenem Ermessen jederzeit ohne Ank&amp;uuml;ndigung ver&amp;auml;ndern und/oder deren Betrieb einstellen. Er ist nicht verpflichtet, Inhalte dieser Website zu aktualisieren.&lt;br /&gt;\r\n&lt;br /&gt;\r\nDer Zugang und die Benutzung dieser Website geschieht auf eigene Gefahr der Benutzer_innen. Der Deutsche Bundesjugendring ist nicht verantwortlich und &amp;uuml;bernimmt keinerlei Haftung f&amp;uuml;r Sch&amp;auml;den, u.a. f&amp;uuml;r direkte, indirekte, zuf&amp;auml;llige, vorab konkret zu bestimmende oder Folgesch&amp;auml;den, die angeblich durch den oder in Verbindung mit dem Zugang und/oder der Benutzung dieser Website aufgetreten sind.&lt;br /&gt;\r\n&lt;br /&gt;\r\nDer Betreiber &amp;uuml;bernimmt keine Verantwortung f&amp;uuml;r die Inhalte und die Verf&amp;uuml;gbarkeit von Websites Dritter, die &amp;uuml;ber externe Links dieses Informationsangebotes erreicht werden. Der Deutsche Bundesjugendring distanziert sich ausdr&amp;uuml;cklich von allen Inhalten, die m&amp;ouml;glicherweise straf- oder haftungsrechtlich relevant sind oder gegen die guten Sitten versto&amp;szlig;en.&lt;br /&gt;\r\n&lt;br /&gt;\r\nSofern innerhalb des Internetangebotes die M&amp;ouml;glichkeit zur Eingabe pers&amp;ouml;nlicher Daten (z.B. E-Mail-Adressen, Namen) besteht, so erfolgt die Preisgabe dieser Daten seitens der Nutzer_innen auf ausdr&amp;uuml;cklich freiwilliger Basis.&lt;br /&gt;\r\n&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;h3&gt;&lt;a id=&quot;textnutzung&quot; name=&quot;textnutzung&quot;&gt;&lt;/a&gt;Nutzungsrechte f&amp;uuml;r Texte und Dateien&lt;/h3&gt;\r\n\r\n&lt;p&gt;Soweit nicht anders angegeben liegen alle Rechte beim Deutschen Bundesjugendring. Davon ausgenommen sind Texte und Dateien unter CreativeCommons-Lizenzen.&lt;/p&gt;\r\n\r\n&lt;p&gt;Soweit im Einzelfall nicht anders geregelt und soweit nicht fremde Rechte betroffen sind, ist die Verbreitung der Dokumente und Bilder von&amp;nbsp;&lt;a href=&quot;http://www.strukturierter-dialog.de/mitmachen&quot;&gt;www.strukturierter-dialog.de/mitmachen&lt;/a&gt; als Ganzes oder in Teilen davon in elektronischer und gedruckter Form unter der Voraussetzung erw&amp;uuml;nscht, dass die Quelle (&lt;a href=&quot;http://www.strukturierter-dialog.de/mitmachen&quot;&gt;www.strukturierter-dialog.de/mitmachen&lt;/a&gt;) genannt wird. Ohne vorherige schriftliche Genehmigung durch den Deutschen Bundesjugendring ist eine kommerzielle Verbreitung der bereitgestellten Texte, Informationen und Bilder ausdr&amp;uuml;cklich untersagt.&lt;/p&gt;\r\n\r\n&lt;p&gt;Nutzungsrechte f&amp;uuml;r Inhalte, die von Nutzer_innen erstellt werden, werden unter einer CreativeCommons-Lizenz zur Verf&amp;uuml;gung gestellt, sofern nicht anders gekennzeichnet.&lt;br /&gt;\r\n&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;h3&gt;&lt;a id=&quot;datenschutz&quot; name=&quot;datenschutz&quot;&gt;&lt;/a&gt;Datenschutzhinweise&lt;/h3&gt;\r\n\r\n&lt;p&gt;Der Deutsche Bundesjugendring nimmt den Schutz personenbezogener Daten sehr ernst. Wir m&amp;ouml;chten, dass jede_r wei&amp;szlig;, wann wir welche Daten erheben und wie wir sie verwenden. Wir haben technische und organisatorische Ma&amp;szlig;nahmen getroffen, die sicherstellen, dass die Vorschriften &amp;uuml;ber den Datenschutz sowohl von uns als auch von externen Dienstleistenden beachtet werden.&lt;br /&gt;\r\n&lt;br /&gt;\r\nIm Zuge der Weiterentwicklung unserer Webseiten und der Implementierung neuer Technologien, um unseren Service zu verbessern, k&amp;ouml;nnen auch &amp;Auml;nderungen dieser Datenschutzerkl&amp;auml;rung erforderlich werden. Daher empfehlen wir, sich diese Datenschutzerkl&amp;auml;rung ab und zu erneut durchzulesen.&lt;br /&gt;\r\n&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;h3&gt;&lt;a id=&quot;zugriff www&quot; name=&quot;zugriff www&quot;&gt;&lt;/a&gt;Zugriff auf das Internetangebot&lt;/h3&gt;\r\n\r\n&lt;p&gt;Jeder Zugriff auf das Internetangebot &lt;a href=&quot;http://www.strukturierter-dialog.de/mitmachen&quot;&gt;www.strukturierter-dialog.de/mitmachen&lt;/a&gt;&amp;nbsp;wird in einer Protokolldatei gespeichert. In der Protokolldatei werden folgende Daten gespeichert:&lt;/p&gt;\r\n\r\n&lt;ul&gt;\r\n	&lt;li&gt;Informationen &amp;uuml;ber die Seite, von der aus die Datei angefordert wurde&lt;/li&gt;\r\n	&lt;li&gt;Name der abgerufenen Datei&lt;/li&gt;\r\n	&lt;li&gt;Datum und Uhrzeit des Abrufs&lt;/li&gt;\r\n	&lt;li&gt;&amp;uuml;bertragene Datenmenge&lt;/li&gt;\r\n	&lt;li&gt;Meldung, ob der Abruf erfolgreich war&lt;/li&gt;\r\n&lt;/ul&gt;\r\n\r\n&lt;p&gt;Die gespeicherten Daten werden ausschlie&amp;szlig;lich zur Optimierung des Internetangebotes ausgewertet. Die Angaben speichern wir auf Servern in Deutschland. Der Zugriff darauf ist nur wenigen, besonders befugten Personen m&amp;ouml;glich, die mit der technischen, kaufm&amp;auml;nnischen oder redaktionellen Betreuung der Server befasst sind.&lt;br /&gt;\r\n&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;h3&gt;&lt;a id=&quot;datenweitergabe&quot; name=&quot;datenweitergabe&quot;&gt;&lt;/a&gt;Weitergabe personenbezogener Daten an Dritte&lt;/h3&gt;\r\n\r\n&lt;p&gt;Daten, die beim Zugriff auf&amp;nbsp;&lt;a href=&quot;http://www.strukturierter-dialog.de/mitmachen&quot;&gt;www.strukturierter-dialog.de/mitmachen&lt;/a&gt;&amp;nbsp;protokolliert worden sind, werden an Dritte nur &amp;uuml;bermittelt, soweit wir gesetzlich oder durch Gerichtsentscheidung dazu verpflichtet sind oder die Weitergabe im Falle von Angriffen auf die Internetinfrastruktur zur Rechts- oder Strafverfolgung erforderlich ist. Eine Weitergabe zu anderen nichtkommerziellen oder zu kommerziellen Zwecken erfolgt nicht.&lt;/p&gt;\r\n\r\n&lt;p&gt;Eingegebene personenbezogene Informationen werden vom Deutschen Bundesjugendring ausschlie&amp;szlig;lich zur Kommunikation mit den Nutzer_ innen verwendet. Wir geben sie ausdr&amp;uuml;cklich nicht an Dritte weiter.&lt;/p&gt;\r\n\r\n&lt;h3&gt;&lt;br /&gt;\r\n&lt;a id=&quot;u18&quot; name=&quot;u18&quot;&gt;&lt;/a&gt;Schutz von Minderj&amp;auml;hrigen&lt;/h3&gt;\r\n\r\n&lt;p&gt;Personen unter 18 Jahren sollten ohne Zustimmung der Eltern oder Erziehungsberechtigten keine personenbezogenen Daten an uns &amp;uuml;bermitteln. Wir fordern keine personenbezogenen Daten von Kindern und Jugendlichen an. Wissentlich sammeln wir solche Daten nicht und geben sie auch nicht an Dritte weiter.&lt;/p&gt;\r\n\r\n&lt;p&gt;F&amp;uuml;r weitere Informationen in Bezug auf die Behandlung von personenbezogenen Daten steht zur Verf&amp;uuml;gung:&lt;/p&gt;\r\n\r\n&lt;p&gt;Michael Scholl&lt;br /&gt;\r\nTelefon: +49 (0)30.400 40-412&lt;br /&gt;\r\nTelefax: +49 (0)30.400 40-422&lt;br /&gt;\r\nE-Mail: &lt;a href=&quot;mailto:info@dbjr.de&quot;&gt;info@dbjr.de&lt;/a&gt;&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;a href=&quot;http://tool.ichmache-politik.de/privacy&quot; target=&quot;_blank&quot;&gt;&lt;strong&gt;&amp;raquo; Weitere Informationen zum Datenschutz&lt;/strong&gt;&lt;/a&gt;&lt;br /&gt;\r\n&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;h2&gt;Gestaltung und Realisierung&lt;/h2&gt;\r\n\r\n&lt;h3&gt;&lt;a id=&quot;design&quot; name=&quot;design&quot;&gt;&lt;/a&gt;Design&lt;/h3&gt;\r\n\r\n&lt;ul&gt;\r\n	&lt;li&gt;&lt;a href=&quot;http://www.die-projektoren.de&quot; target=&quot;_blank&quot;&gt;DIE.PROJEKTOREN &amp;ndash; FARYS &amp;amp; RUSCH GBR&lt;/a&gt;&amp;nbsp;&lt;/li&gt;\r\n&lt;/ul&gt;\r\n\r\n&lt;h3&gt;&lt;a id=&quot;progammierung&quot; name=&quot;progammierung&quot;&gt;&lt;/a&gt;Programmierung&lt;/h3&gt;\r\n\r\n&lt;ul&gt;\r\n	&lt;li&gt;&lt;a href=&quot;http://bohnetlingua.de/&quot;&gt;Anne Bohnet&lt;/a&gt;&lt;/li&gt;\r\n	&lt;li&gt;&lt;a href=&quot;http://www.digitalroyal.de&quot; target=&quot;_blank&quot;&gt;Digital Royal GmbH&amp;nbsp;&lt;/a&gt;&lt;/li&gt;\r\n	&lt;li&gt;Tim Schrock&lt;/li&gt;\r\n	&lt;li&gt;&lt;a href=&quot;http://www.seitenmeister.com&quot; target=&quot;_blank&quot;&gt;seitenmeister&lt;/a&gt;&amp;nbsp;&lt;/li&gt;\r\n	&lt;li&gt;Synergic&lt;/li&gt;\r\n	&lt;li&gt;&lt;a href=&quot;http://www.xima.de&quot; target=&quot;_blank&quot;&gt;xima media GmbH &lt;/a&gt;&lt;/li&gt;\r\n&lt;/ul&gt;\r\n\r\n&lt;h3&gt;&lt;a id=&quot;software&quot; name=&quot;software&quot;&gt;&lt;/a&gt;Verwendete Software&lt;/h3&gt;\r\n\r\n&lt;p&gt;Das Internetangebot &lt;sup&gt;e&lt;/sup&gt;Partool basiert auf quelloffener Software. Wir verwenden unter anderem&lt;/p&gt;\r\n\r\n&lt;ul&gt;\r\n	&lt;li&gt;&lt;a href=&quot;https://httpd.apache.org/&quot; target=&quot;_blank&quot;&gt;Apache Webserver&lt;/a&gt;&amp;nbsp;mit &lt;a href=&quot;http://php.net/&quot; target=&quot;_blank&quot;&gt;PHP&lt;/a&gt;&amp;nbsp;und &lt;a href=&quot;http://mysql.com/&quot; target=&quot;_blank&quot;&gt;MySQL-Datenbanken&lt;/a&gt;&lt;/li&gt;\r\n	&lt;li&gt;&lt;a href=&quot;http://twitter.github.io/bootstrap/&quot; target=&quot;_blank&quot;&gt;Bootstrap&lt;/a&gt;&lt;/li&gt;\r\n	&lt;li&gt;&lt;a href=&quot;http://ckeditor.com/&quot; target=&quot;_blank&quot;&gt;CKEditor&lt;/a&gt;&lt;/li&gt;\r\n	&lt;li&gt;&lt;a href=&quot;http://jquery.com/&quot; target=&quot;_blank&quot;&gt;jQuery&lt;/a&gt;&lt;/li&gt;\r\n	&lt;li&gt;&lt;a href=&quot;http://framework.zend.com/&quot; target=&quot;_blank&quot;&gt;Zend PHP Framework&lt;/a&gt;&lt;/li&gt;\r\n&lt;/ul&gt;\r\n\r\n&lt;p&gt;&lt;br /&gt;\r\nBerlin im Mai 2013&lt;/p&gt;\r\n\r\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;\r\n', '', 0),
(167, 1, 'xx', 'So geht''s', 'n', 'cnslt_info', '&lt;h1&gt;&lt;strong&gt;&lt;a id=&quot;So geht''s&quot; name=&quot;So geht''s&quot;&gt;&lt;/a&gt;So geht&amp;#39;s&lt;/strong&gt;&lt;/h1&gt;\r\n\r\n&lt;p&gt;&lt;span style=&quot;color:#008080;&quot;&gt;Links angeordnet findet ihr die Inhalts-, oder auch Artikelseiten, die ihr im Adminbereich frei anlegen und bearbeiten k&amp;ouml;nnt. Eine k&amp;ouml;nnte &amp;quot;So geht&amp;#39;s&amp;quot; hei&amp;szlig;en. Hier erkl&amp;auml;rt ihr wie euer Projekt funktioniert, wie die Teilnehmenden sich beteiligen k&amp;ouml;nnen. Wir von Ichmache&amp;gt;Politik haben das mithilfe eines Clips gemacht:&lt;/span&gt;&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;iframe allowfullscreen=&quot;&quot; frameborder=&quot;0&quot; height=&quot;315&quot; src=&quot;https://www.youtube.com/embed/mTe4JBVZt3Y&quot; width=&quot;420&quot;&gt;&lt;/iframe&gt;&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;span style=&quot;color:#008080;&quot;&gt;Dann w&amp;auml;ren an dieser Stelle noch ein paar Infos zu eurer Beteiligungsrunde gut, z.B.:&lt;/span&gt;&lt;/p&gt;\r\n\r\n&lt;p&gt;Setzt euch auf eure Art, bei euch vor Ort, in der Gruppe/Klasse/Clique mit den Fragen und Themen unter dem Motto &lt;strong&gt;&amp;quot;Unsere erste Beteiligungsrunde&amp;quot; &lt;/strong&gt;auseinander und entwickelt Positionen und Vorschl&amp;auml;ge dazu. Bis zum --.--.20-- habt ihr Zeit, uns eure Ergebnisse &amp;uuml;ber das &lt;strong&gt;ePartool &lt;/strong&gt;online mitzuteilen.&lt;/p&gt;\r\n\r\n&lt;p&gt;In einer zweiten Phase (--.-- &amp;ndash; --.--.20--) k&amp;ouml;nnen alle, die mitgemacht haben, aus den gesammelten Beitr&amp;auml;gen diejenigen ausw&amp;auml;hlen, die am wichtigsten sind. Wir sorgen dann daf&amp;uuml;r, dass eure Positionen in die Diskussionen und Debatten im Rahmen von ... einflie&amp;szlig;en und ihr ein Feedback dazu erhaltet.&lt;br /&gt;\r\n&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;h1&gt;&lt;strong&gt;&lt;a id=&quot;So weiter&quot; name=&quot;So weiter&quot;&gt;&lt;/a&gt;So geht&amp;rsquo;s weiter&lt;/strong&gt;&lt;/h1&gt;\r\n\r\n&lt;p&gt;&lt;span style=&quot;color:#008080;&quot;&gt;Sagt den Teilnehmenden an dieser Stelle am Besten noch wie es mit den Ergebnissen der Runde, bzw. eurem Projekt weitergeht. Dann m&amp;uuml;ssten alle wichtigen Fragen gekl&amp;auml;rt sein. &lt;strong&gt;Oder?...&lt;/strong&gt;&lt;/span&gt;&lt;/p&gt;\r\n\r\n&lt;h1&gt;&lt;br /&gt;\r\n&lt;strong&gt;&lt;a id=&quot;Was ihr noch wissen solltet&quot; name=&quot;Was ihr noch wissen solltet&quot;&gt;&lt;/a&gt;Was ihr noch wissen solltet&lt;/strong&gt;&lt;/h1&gt;\r\n\r\n&lt;p&gt;&lt;span style=&quot;color:#008080;&quot;&gt;&lt;strong&gt;... Noch nicht ganz! So ein paar Hinweise zum Eintragen der Beitr&amp;auml;ge schaden sicherlich nicht. Ihr k&amp;ouml;nnt euch hierbei an unserem Text bedienen:&lt;/strong&gt;&lt;/span&gt;&lt;/p&gt;\r\n\r\n&lt;p&gt;Wir haben uns bem&amp;uuml;ht, euch das Eintragen von Beitr&amp;auml;gen so einfach wie m&amp;ouml;glich zu machen. Fangt einfach mit dem Schreiben an! Das Einzige, was wir am Ende von euch ben&amp;ouml;tigen, ist eine funktionierende E-Mail-Adresse. Eure Beitr&amp;auml;ge k&amp;ouml;nnen von anderen Besucher_innen dieser Website gelesen werden, eure E-Mail-Adresse wird aber nicht ver&amp;ouml;ffentlicht.&lt;/p&gt;\r\n\r\n&lt;p&gt;Bitte formuliert eure Beitr&amp;auml;ge m&amp;ouml;glichst knapp und beschr&amp;auml;nkt euch pro Box auf eine Idee bzw. einen Gedanken (max. 300 Buchstaben). Das macht es beim Abstimmen leichter zu entscheiden, welche Beitr&amp;auml;ge besonders wichtig sind und hilft uns, die Beitr&amp;auml;ge am Ende zusammenzufassen. F&amp;uuml;r Erkl&amp;auml;rungen, weitergehende Infos usw. nutzt bitte die Erl&amp;auml;uterungsbox. Ihr k&amp;ouml;nnt so viele Beitr&amp;auml;ge eintragen, wie ihr m&amp;ouml;chtet. Klickt dazu einfach auf den Button &amp;bdquo;Neuer Eintrag&amp;ldquo; und eine neue Box &amp;ouml;ffnet sich.&lt;/p&gt;\r\n\r\n&lt;p&gt;Die Reihenfolge, in der ihr die Fragen beantwortet, k&amp;ouml;nnt ihr frei w&amp;auml;hlen. Genauso k&amp;ouml;nnt ihr entscheiden, ob ihr eine oder mehrere Fragen beantwortet. Es ist m&amp;ouml;glich, eine Pause zu machen und das Eintragen an einem anderen Tag fortzusetzen. Eure Beitr&amp;auml;ge k&amp;ouml;nnt ihr sp&amp;auml;ter ganz einfach erg&amp;auml;nzen, bearbeiten oder auch wieder l&amp;ouml;schen.&lt;/p&gt;\r\n\r\n&lt;p&gt;Um das Eintragen zu unterbrechen bzw. abzuschlie&amp;szlig;en, klickt auf den Button &amp;bdquo;Pause / Beenden&amp;ldquo;. Ihr werdet dann nach einer E-Mail-Adresse gefragt und um ein paar weitere Angaben gebeten, deren Beantwortung freiwillig ist. Nur die Angabe einer E-Mail-Adresse und ob ihr als Einzelperson oder als Gruppe antwortet, ist verpflichtend. Wenn ihr euch als Gruppe beteiligt, fragen wir auch deren Gr&amp;ouml;&amp;szlig;e ab. Das ist f&amp;uuml;r die 2. Phase der Beteiligungsrunde, die Abstimmung, &amp;nbsp;wichtig.&lt;/p&gt;\r\n\r\n&lt;p&gt;An die von euch eingetragene E-Mail-Adresse schicken wir automatisch eine E-Mail mit einem Best&amp;auml;tigungslink, den ihr aktivieren m&amp;uuml;sst, indem ihr darauf klickt oder ihn in euren Internetbrowser kopiert. Erst dann werden eure Eintr&amp;auml;ge endg&amp;uuml;ltig gespeichert und ver&amp;ouml;ffentlicht.&lt;/p&gt;\r\n\r\n&lt;p&gt;Mit der E-Mail erhaltet ihr auch automatisch ein Passwort. Dieses ben&amp;ouml;tigt ihr, wenn ihr zu einem sp&amp;auml;teren Zeitpunkt eure Eintr&amp;auml;ge erg&amp;auml;nzen oder bearbeiten wollt. Ihr solltet unsere E-Mail also f&amp;uuml;r einige Tage aufbewahren!&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;em&gt;&amp;Uuml;brigens:&lt;/em&gt; Das Projektteam &amp;uuml;berpr&amp;uuml;ft alle Eintr&amp;auml;ge und beh&amp;auml;lt sich vor, diese wenn n&amp;ouml;tig zu sperren.&lt;/p&gt;\r\n', '&lt;h4&gt;&lt;strong&gt;&lt;a href=&quot;#So geht''s&quot;&gt;So geht&amp;#39;s&lt;/a&gt;&lt;/strong&gt;&lt;/h4&gt;\r\n\r\n&lt;p&gt;&lt;strong&gt;&lt;a href=&quot;#So weiter&quot;&gt;So geht&amp;#39;s weiter&lt;/a&gt;&lt;/strong&gt;&lt;/p&gt;\r\n\r\n&lt;h4&gt;&lt;strong&gt;&lt;a href=&quot;#Was ihr noch wissen solltet&quot;&gt;Was ihr noch wissen solltet&lt;/a&gt;&lt;/strong&gt;&lt;/h4&gt;\r\n\r\n&lt;p&gt;&lt;span style=&quot;color:#008080;&quot;&gt;(Hier k&amp;ouml;nnt ihr noch Anker der Inhaltsseite verlinken, das erleichtert die Nutzerf&amp;uuml;hrung. V.a. bei viel Text.)&lt;/span&gt;&lt;/p&gt;\r\n', 0),
(168, 1, 'xx', 'Infos zum Thema', 'n', 'cnslt_backgr', '&lt;h1&gt;Infos zum Thema&lt;/h1&gt;\r\n\r\n&lt;p&gt;Es ist immer gut den Teilnehmenden noch ein paar Hintergrundinformationen zu der Beteiligungsrunde zur Verf&amp;uuml;gung zu stellen. Diese k&amp;ouml;nnen sie dann z.B. f&amp;uuml;r die Gruppenarbeit vor Ort nutzen, ohne sich vorher einer umfangreichen Recherche zu dem Thema widmen zu m&amp;uuml;ssen. Wie ihr die Informationen gliedert und in welchem Umfang sie sind, as ist nat&amp;uuml;rlich euch &amp;uuml;berlassen.&lt;/p&gt;\r\n\r\n&lt;p&gt;Bei mehreren Fragen macht es Sinn die Hintergrundinformationen nach den Fragen der Runde zu untergliedern, indem ihr Unterseiten f&amp;uuml;r die &amp;quot;Infos zum Thema&amp;quot; anlegt. Diese k&amp;ouml;nnt ihr dann auch hier verlinken:&lt;/p&gt;\r\n\r\n&lt;h2&gt;&lt;a href=&quot;https://tool.ichmache-politik.de/article/show/kid/19/aid/173&quot;&gt;&amp;gt;&amp;gt; Infos zu Frage 1&lt;/a&gt;&lt;/h2&gt;\r\n\r\n&lt;h2&gt;&lt;a href=&quot;https://tool.ichmache-politik.de/article/show/kid/19/aid/174&quot;&gt;&amp;gt;&amp;gt; Infos zu Frage 2&lt;/a&gt;&lt;/h2&gt;\r\n\r\n&lt;h2&gt;&lt;a href=&quot;https://tool.ichmache-politik.de/article/show/kid/19/aid/175&quot;&gt;&amp;gt;&amp;gt; Infos zu Frage 3&lt;/a&gt;&lt;/h2&gt;\r\n', '', 0),
(173, 1, 'xx', 'Infos zu Frage 1', 'n', '0', '&lt;h1&gt;Infos zu Frage 1&lt;/h1&gt;\r\n\r\n&lt;p&gt;Hier k&amp;ouml;nnt ihr alle wichtigen Hintergrundeinformationen zu dieser Frage einstellen: Texte, Artikel, Bilder, Videos und weiterf&amp;uuml;hrende Links, d&amp;uuml;rfen nat&amp;uuml;rlich auch nicht fehlen.&lt;/p&gt;\r\n', '', 168),
(169, 1, 'xx', 'Praxishilfen', 'n', 'cnslt_info', '&lt;h1&gt;Praxishilfen&lt;/h1&gt;\r\n\r\n&lt;p&gt;&lt;span style=&quot;color:#008080;&quot;&gt;Auch Praxishilfen zur Auseinandersetzung vor Ort d&amp;uuml;rfen nicht fehlen! Hier findet ihr eine kleine Formulierungshilfe:&lt;/span&gt;&lt;/p&gt;\r\n\r\n&lt;p&gt;Hier findet ihr konkrete Vorschl&amp;auml;ge f&amp;uuml;r die Auseinandersetzung mit dem Thema &lt;strong&gt;&amp;quot;Unsere erste Beteiligungsrunde&amp;quot;&lt;/strong&gt; in eurer Gruppe vor Ort. Mit diesen zus&amp;auml;tzlichen Informationen und den Methoden k&amp;ouml;nnt ihr euch zusammen in eurer Gruppe auseinandersetzen, Stellung nehmen und L&amp;ouml;sungsvorschl&amp;auml;ge zu den unterschiedlichen Problemstellungen erarbeiten und &amp;uuml;ber das&amp;nbsp;&lt;sup style=&quot;position: relative; font-size: 10px; line-height: 0; vertical-align: baseline; top: -0.5em; &quot;&gt;e&lt;/sup&gt;Partool einbringen. Weitere Methoden findet ihr in unserem &lt;a href=&quot;https://tool.ichmache-politik.de/article/show/kid/19/aid/170&quot;&gt;&amp;gt;&amp;gt; METHODENPOOL&lt;/a&gt;.&lt;/p&gt;\r\n', '', 0);
INSERT INTO `articles` (`art_id`, `kid`, `proj`, `desc`, `hid`, `ref_nm`, `artcl`, `sidebar`, `parent_id`) VALUES
(170, 1, 'xx', 'Methodenpool', 'n', 'cnslt_info', '&lt;pre style=&quot;margin: 0px 0px 0.5em; font-family: Arial,sans-serif; font-weight: bold; line-height: 1.5em; color: rgb(68, 68, 68); text-rendering: optimizelegibility; font-size: 1.1em;&quot;&gt;\r\n&lt;small&gt;&lt;a id=&quot;oben&quot; name=&quot;oben&quot;&gt;&lt;/a&gt;Um zu tollen Ideen und Ergebnissen zu kommen gibt es viele Wege. Wir m&amp;ouml;chten euch hier kurz ein paar Methoden aufzeigen, die ihr einfach in eurer Gruppe an-wenden k&amp;ouml;nnt. Die dargestellten Methoden k&amp;ouml;nnt ihr als pdf herunterladen oder in Form unserer &lt;a href=&quot;http://tool.ichmache-politik.de/media/misc/Methodenkarten.JPG&quot; target=&quot;_blank&quot;&gt;Methodenkarten&lt;/a&gt; bei uns bestellen. &lt;/small&gt;&lt;/pre&gt;\r\n\r\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;img alt=&quot;&quot; src=&quot;/media/consultations/19/grstscreen.JPG&quot; style=&quot;width: 150px; height: 106px; float: left;&quot; /&gt;&amp;nbsp; Auch unsere Gruppenstunde ist nicht zu untersch&amp;auml;tzen. Schaut doch mal rein und lasst euch inspirieren!&lt;br /&gt;\r\n&amp;nbsp; &lt;a href=&quot;/media/consultations/19/gruppenstunde_RZ.pdf&quot;&gt;&amp;gt;&amp;gt;&amp;gt; Zur Gruppenstunde von Ichmache&amp;gt;Politik&lt;/a&gt;&lt;/p&gt;\r\n\r\n&lt;h1&gt;&amp;nbsp;&lt;/h1&gt;\r\n\r\n&lt;h1&gt;&amp;nbsp;&lt;/h1&gt;\r\n\r\n&lt;h1&gt;&lt;strong&gt;Methoden offline&lt;/strong&gt;&lt;/h1&gt;\r\n\r\n&lt;h4 style=&quot;margin: 0px 0px 0.5em; font-family: Arial,sans-serif; font-weight: bold; line-height: 1.5em; color: rgb(68, 68, 68); text-rendering: optimizelegibility; font-size: 1.1em;&quot;&gt;&lt;u&gt;&lt;a id=&quot;Brainstorming&quot; name=&quot;Brainstorming&quot;&gt;&lt;/a&gt;Brainstorming&lt;/u&gt;&amp;nbsp; &lt;a href=&quot;http://tool.ichmache-politik.de/media/misc/Brainstorming.pdf&quot; target=&quot;_blank&quot;&gt;&lt;em&gt;(pdf-Download)&lt;/em&gt;&lt;/a&gt;&lt;/h4&gt;\r\n\r\n&lt;p&gt;&lt;em&gt;&lt;strong&gt;Ziele:&lt;/strong&gt;&amp;nbsp;&lt;/em&gt;Phantasieanregung, Ideensammlung&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;em&gt;&lt;strong&gt;Gruppengr&amp;ouml;&amp;szlig;e:&lt;/strong&gt;&amp;nbsp;&lt;/em&gt;F&amp;uuml;r Kleingruppen geeignet&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;strong&gt;&lt;em&gt;Zeitbedarf&lt;/em&gt;&lt;/strong&gt;: ca. 10 - 30 Minuten,&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;em&gt;&lt;strong&gt;Materialbedarf:&lt;/strong&gt;&lt;/em&gt; Karten, Stifte, Stellwand.&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;strong&gt;&lt;em&gt;Ablauf:&lt;/em&gt;&lt;/strong&gt;&amp;nbsp;Der oder die Moderator_in gibt ein Thema f&amp;uuml;r den &amp;bdquo;Gedankensturm&amp;ldquo; vor. Die Teilnehmenden schreiben ihre eigenen L&amp;ouml;sungsideen auf Karten. Wichtig ist hier: Nur ein Thema pro Karte und dieses gro&amp;szlig; und deutlich aufschreiben. Die ausgef&amp;uuml;llten Karten werden im Anschluss eingesammelt und auf eine Stellwand geheftet. Die Ideen werden dabei thematisch geordnet. Angeregt durch die Karten entwickeln sich bei den Teilnehmenden h&amp;auml;ufig weitere Ideen, die erg&amp;auml;nzend aufgenommen werden k&amp;ouml;nnen. In der Auswahl- und Entscheidungsphase werden die zuvor genannten Ideen nach Einfachheit, Realisierbarkeit und Schwierigkeitsgrad bewertet....&lt;br /&gt;\r\n&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;hr style=&quot;margin: 0px; border-bottom-width: 1px; border-top-color: rgba(102, 102, 102, 0.246094); border-bottom-style: solid; border-bottom-color: rgb(255, 255, 255); color: rgb(68, 68, 68); font-family: Arial, sans-serif; &quot; /&gt;\r\n&lt;h3 style=&quot;margin: 0px 0px 0.5em; font-family: Arial, sans-serif; font-weight: bold; line-height: 1.5em; color: rgb(68, 68, 68); text-rendering: optimizelegibility; font-size: 1.1em; &quot;&gt;&lt;a id=&quot;Kopf&quot; name=&quot;Kopf&quot;&gt;&lt;/a&gt;&lt;u&gt;Kopfstand-Methode&lt;/u&gt; &lt;a href=&quot;http://tool.ichmache-politik.de/media/misc/Kopfstand.pdf&quot; target=&quot;_blank&quot;&gt;(pdf-Download)&lt;/a&gt;&lt;/h3&gt;\r\n\r\n&lt;p&gt;&lt;em&gt;&lt;strong&gt;Ziele:&lt;/strong&gt;&amp;nbsp;&lt;/em&gt;Spa&amp;szlig;, Phantasieanregung, Ideensammlung.&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;em&gt;&lt;strong&gt;Gruppengr&amp;ouml;&amp;szlig;e:&lt;/strong&gt;&amp;nbsp;&lt;/em&gt;F&amp;uuml;r Kleingruppen geeignet&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;em&gt;&lt;strong&gt;Zeitbedarf:&lt;/strong&gt;&lt;/em&gt; ca. 10 - 30 Minuten,&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;em&gt;&lt;strong&gt;Materialbedarf:&lt;/strong&gt;&lt;/em&gt; Karten, Stifte, Stellwand.&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;em&gt;&lt;strong&gt;Ablauf:&amp;nbsp;&lt;/strong&gt;&lt;/em&gt;F&amp;uuml;r die L&amp;ouml;sungsfindung wird ein Brainstorming durchgef&amp;uuml;hrt, bei dem zuerst von dem Gegenteil der gesuchten L&amp;ouml;sung ausgegangen wird. Die Problemfrage wird auf den Kopf gestellt, also ins Gegenteil gekehrt. Statt beispielsweise danach zu fragen &amp;bdquo;Wie schaffen wir es, neue Jugendliche f&amp;uuml;r unseren Jugendclub zu gewinnen?&amp;ldquo; kehrt Ihr die Zielfrage um und sucht Antworten auf die Frage &amp;bdquo;Wie gestalten wir unseren Jugendclub m&amp;ouml;glichst unattraktiv f&amp;uuml;r Neue?&amp;ldquo; ...&lt;br /&gt;\r\n&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;hr style=&quot;margin: 0px; border-bottom-width: 1px; border-top-color: rgba(102, 102, 102, 0.246094); border-bottom-style: solid; border-bottom-color: rgb(255, 255, 255); color: rgb(68, 68, 68); font-family: Arial, sans-serif; &quot; /&gt;\r\n&lt;h3 style=&quot;margin: 0px 0px 0.5em; font-family: Arial, sans-serif; font-weight: bold; line-height: 1.5em; color: rgb(68, 68, 68); text-rendering: optimizelegibility; font-size: 1.1em; &quot;&gt;&lt;u&gt;&lt;a id=&quot;Kugel&quot; name=&quot;Kugel&quot;&gt;&lt;/a&gt;Kugellager&lt;/u&gt; &lt;a href=&quot;http://tool.ichmache-politik.de/media/misc/Kugellager.pdf&quot; target=&quot;_blank&quot;&gt;(pdf-Download)&lt;/a&gt;&lt;/h3&gt;\r\n\r\n&lt;p&gt;&lt;em&gt;&lt;strong&gt;Ziele:&lt;/strong&gt;&amp;nbsp;&lt;/em&gt;Alle Teilnehmenden aktivieren, Einstieg ins Thema, Ideensammlung&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;em&gt;&lt;strong&gt;Gruppengr&amp;ouml;&amp;szlig;e&lt;/strong&gt;: &lt;/em&gt;F&amp;uuml;r jede Gruppengr&amp;ouml;&amp;szlig;e geeignet (am besten ist hier eine gerade Anzahl von Teilnehmenden),&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;em&gt;&lt;strong&gt;Zeitbedarf:&lt;/strong&gt;&lt;/em&gt; ca. 10 - 30 Minuten,&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;em&gt;&lt;strong&gt;Materialbedarf:&lt;/strong&gt;&lt;/em&gt; (evtl. ben&amp;ouml;tigt ihr Tische und Schreibzeug).&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;strong&gt;&lt;em&gt;Ablauf:&lt;/em&gt;&lt;/strong&gt;&amp;nbsp;Es geht darum, dass alle Teilnehmenden kurz ihre Gedanken und Ideen miteinander austauschen. Hierf&amp;uuml;r stellt sich die Gruppe in zwei Kreise (einen inneren und einen &amp;auml;u&amp;szlig;eren) so auf, dass sich jeweils eine Person aus dem inneren Kreis einer Person aus dem &amp;auml;u&amp;szlig;eren Kreis gegen&amp;uuml;ber sieht. So werden die Paare f&amp;uuml;r den Dialog gesetzt. Nun wird eine kurze Frage bzw. Aufgabe gestellt und von den jeweiligen P&amp;auml;rchen miteinander diskutiert...&lt;/p&gt;\r\n\r\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;hr style=&quot;border-bottom-width: 1px; border-bottom-style: solid; border-bottom-color: rgb(255, 255, 255); border-top-color: rgba(102, 102, 102, 0.246094); margin: 0px; color: rgb(68, 68, 68); font-family: Arial, sans-serif; &quot; /&gt;\r\n&lt;h3 style=&quot;margin: 0px 0px 0.5em; font-family: Arial, sans-serif; font-weight: bold; line-height: 1.5em; color: rgb(68, 68, 68); text-rendering: optimizelegibility; font-size: 1.1em; &quot;&gt;&lt;a id=&quot;6-3-5&quot; name=&quot;6-3-5&quot;&gt;&lt;/a&gt;&lt;u&gt;6-3-5 Methode&lt;/u&gt;&amp;nbsp; &lt;a href=&quot;http://tool.ichmache-politik.de/media/misc/6-3-5%20Methode.pdf&quot; target=&quot;_blank&quot;&gt;(pdf-Download)&lt;/a&gt;&lt;/h3&gt;\r\n\r\n&lt;p&gt;&lt;em&gt;&lt;strong&gt;Ziele:&lt;/strong&gt;&amp;nbsp;&lt;/em&gt;Ideenfindung, Kreativit&amp;auml;t&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;em&gt;&lt;strong&gt;Gruppengr&amp;ouml;&amp;szlig;e&lt;/strong&gt;: &lt;/em&gt;F&amp;uuml;r jede Gruppengr&amp;ouml;&amp;szlig;e geeignet&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;em&gt;&lt;strong&gt;Zeitbedarf:&lt;/strong&gt;&lt;/em&gt; ca. 10 - 30 Minuten,&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;em&gt;&lt;strong&gt;Materialbedarf:&lt;/strong&gt;&lt;/em&gt; Papier, Stifte&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;strong&gt;&lt;em&gt;Ablauf:&lt;/em&gt;&lt;/strong&gt;&amp;nbsp;6 Teilnehmer_innen entwickeln 3 Ideen jeweils f&amp;uuml;nfmal weiter.Jede_r von Euch hat ein Blatt mit einer Tabelle vor sich liegen. Die Tabelle besteht aus drei Spalten und sechs Zeilen. Jede_r entwirft drei L&amp;ouml;sungsvorschl&amp;auml;ge und gibt dann sein/ ihr Blatt einen Platz nach links weiter....&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;strong&gt;&lt;a href=&quot;#oben&quot; style=&quot;color: rgb(1, 150, 188); text-decoration: none; &quot;&gt;&amp;gt;&amp;gt; nach oben&lt;/a&gt;&lt;/strong&gt;&lt;/p&gt;\r\n\r\n&lt;hr /&gt;\r\n&lt;h3 style=&quot;margin: 0px 0px 0.5em; font-family: Arial, sans-serif; font-weight: bold; line-height: 1.5em; color: rgb(68, 68, 68); text-rendering: optimizelegibility; font-size: 1.1em; &quot;&gt;&lt;a id=&quot;Mind&quot; name=&quot;Mind&quot;&gt;&lt;/a&gt;&lt;u&gt;Mindmapping&lt;/u&gt; &lt;a href=&quot;http://tool.ichmache-politik.de/media/misc/5%20Mindmapping.pdf&quot; target=&quot;_blank&quot;&gt;(pdf-Download)&lt;/a&gt;&lt;/h3&gt;\r\n\r\n&lt;p&gt;&lt;em&gt;&lt;strong&gt;Ziele:&lt;/strong&gt;&amp;nbsp;&lt;/em&gt;Ideenfindung/Zieldefinitionen, Assoziationen, Wissens abfrage, Strukturierung komplexer Themen, visualisierte Ergebnissicherung&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;em&gt;&lt;strong&gt;Gruppengr&amp;ouml;&amp;szlig;e&lt;/strong&gt;: &lt;/em&gt;F&amp;uuml;r fast jede Gruppengr&amp;ouml;&amp;szlig;e geeignet&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;em&gt;&lt;strong&gt;Zeitbedarf:&lt;/strong&gt;&lt;/em&gt; ca. 10 - 30 Minuten,&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;em&gt;&lt;strong&gt;Materialbedarf:&lt;/strong&gt;&lt;/em&gt; gro&amp;szlig;e Plakate, Eddings&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;strong&gt;&lt;em&gt;Ablauf:&lt;/em&gt;&lt;/strong&gt; Mind-Mapping ist eine spezielle Art sich &amp;uuml;bersichtlich Notizen zu machen. Mindmaps k&amp;ouml;nnen &amp;ndash; je nach Gruppengr&amp;ouml;&amp;szlig;e &amp;ndash; in Einzel- oder Kleingruppenarbeit erstellt werden. Von dem in der Mitte des Blattes dargestellten Thema zieht Ihr &amp;Auml;ste, diese stellen die jeweiligen Hauptpunkte oder die Grobgliederung des Themas dar. Die einzelnen &amp;Auml;ste k&amp;ouml;nnt Ihr mit Symbolen und Zeichnungen beschriften, so k&amp;ouml;nnt Ihr die jeweiligen Punkte schneller erkennen....&lt;/p&gt;\r\n\r\n&lt;hr style=&quot;border-bottom-width: 1px; border-bottom-style: solid; border-bottom-color: rgb(255, 255, 255); border-top-color: rgba(102, 102, 102, 0.246094); margin: 0px; color: rgb(68, 68, 68); font-family: Arial, sans-serif; &quot; /&gt;\r\n&lt;h3 style=&quot;margin: 0px 0px 0.5em; font-family: Arial, sans-serif; font-weight: bold; line-height: 1.5em; color: rgb(68, 68, 68); text-rendering: optimizelegibility; font-size: 1.1em; &quot;&gt;&lt;u&gt;&lt;a id=&quot;Stein&quot; name=&quot;Stein&quot;&gt;&lt;/a&gt;Stolpersteine&lt;/u&gt;&amp;nbsp; &lt;a href=&quot;http://tool.ichmache-politik.de/media/misc/6%20Stolpersteine.pdf&quot; target=&quot;_blank&quot;&gt;(pdf-Download)&lt;/a&gt;&lt;/h3&gt;\r\n\r\n&lt;p&gt;&lt;em&gt;&lt;strong&gt;Ziele:&lt;/strong&gt;&amp;nbsp;&lt;/em&gt;Zieldefinitionen, Problemlagen bewusst machen, L&amp;ouml;sungen entwickeln&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;em&gt;&lt;strong&gt;Gruppengr&amp;ouml;&amp;szlig;e&lt;/strong&gt;: &lt;/em&gt;F&amp;uuml;r Kleingruppen geeignet (5-13 Pers)&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;em&gt;&lt;strong&gt;Zeitbedarf:&lt;/strong&gt;&lt;/em&gt; ca. 10 - 30 Minuten,&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;em&gt;&lt;strong&gt;Materialbedarf:&lt;/strong&gt;&lt;/em&gt; Schuhkartons,Pappe, Schere, Kleber, Eddings&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;strong&gt;&lt;em&gt;Ablauf:&lt;/em&gt;&lt;/strong&gt;&amp;nbsp;Gemeinsam mit dem/der Moderator_in sammelt Ihr Punkte, die Euch st&amp;ouml;ren, behindern oder blockieren. Die von Euch genannten Punkte schreibt Ihr einzeln auf Schuhkartons, aus denen Ihr eine Mauer baut, die Euch den Weg versperrt.Die Probleme werden nun nach einander angesprochen und alle suchen nach L&amp;ouml;sungsm&amp;ouml;glichkeiten....&lt;/p&gt;\r\n\r\n&lt;hr style=&quot;border-bottom-width: 1px; border-bottom-style: solid; border-bottom-color: rgb(255, 255, 255); border-top-color: rgba(102, 102, 102, 0.246094); margin: 0px; color: rgb(68, 68, 68); font-family: Arial, sans-serif; &quot; /&gt;\r\n&lt;h3 style=&quot;margin: 0px 0px 0.5em; font-family: Arial, sans-serif; font-weight: bold; line-height: 1.5em; color: rgb(68, 68, 68); text-rendering: optimizelegibility; font-size: 1.1em; &quot;&gt;&lt;a id=&quot;Bord&quot; name=&quot;Bord&quot;&gt;&lt;/a&gt;&lt;u&gt;Alles an Bord&lt;/u&gt; &lt;a href=&quot;http://tool.ichmache-politik.de/media/misc/7%20Alles%20an%20Bord.pdf&quot; target=&quot;_blank&quot;&gt;(pdf-Download)&lt;/a&gt;&lt;/h3&gt;\r\n\r\n&lt;p&gt;&lt;em&gt;&lt;strong&gt;Ziele:&lt;/strong&gt;&amp;nbsp;&lt;/em&gt;Zieldefinitionen, Themenfindung nach Wichtigkeit und Interesse&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;em&gt;&lt;strong&gt;Gruppengr&amp;ouml;&amp;szlig;e&lt;/strong&gt;: &lt;/em&gt;F&amp;uuml;r Kleinruppen geeignet&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;em&gt;&lt;strong&gt;Zeitbedarf:&lt;/strong&gt;&lt;/em&gt; ca. 30- 45 Minuten,&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;em&gt;&lt;strong&gt;Materialbedarf:&lt;/strong&gt;&lt;/em&gt; Papierw&amp;auml;nde, Stifte, Papierb&amp;ouml;gen&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;strong&gt;&lt;em&gt;Ablauf:&lt;/em&gt;&lt;/strong&gt;&amp;nbsp;Zun&amp;auml;chst schreibt der/die Moderator_in, die f&amp;uuml;r Euch wichtigen Themen auf einem Plakat auf. Noch sind alle Themen mit an Bord. Da das Boot ein Leck hat, k&amp;ouml;nnen nicht alle Themen ans rettende Ufer mitgenommen werden. Jede_r schreibt auf einen Zettel die f&amp;uuml;nf f&amp;uuml;r ihn wichtigsten Themen in einer selbst gew&amp;auml;hlten Reihenfolge auf.....&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;strong&gt;&lt;a href=&quot;#oben&quot; style=&quot;color: rgb(1, 150, 188); text-decoration: none; &quot;&gt;&amp;gt;&amp;gt; nach oben&lt;/a&gt;&lt;/strong&gt;&lt;/p&gt;\r\n\r\n&lt;hr style=&quot;border-bottom-width: 1px; border-bottom-style: solid; border-bottom-color: rgb(255, 255, 255); border-top-color: rgba(102, 102, 102, 0.246094); margin: 0px; color: rgb(68, 68, 68); font-family: Arial, sans-serif; &quot; /&gt;\r\n&lt;h3 style=&quot;margin: 0px 0px 0.5em; font-family: Arial, sans-serif; font-weight: bold; line-height: 1.5em; color: rgb(68, 68, 68); text-rendering: optimizelegibility; font-size: 1.1em; &quot;&gt;&lt;a id=&quot;Plan&quot; name=&quot;Plan&quot;&gt;&lt;/a&gt;&lt;u&gt;Das Planspiel&lt;/u&gt; &lt;a href=&quot;http://tool.ichmache-politik.de/media/misc/8%20Das%20Planspiel.pdf&quot; target=&quot;_blank&quot;&gt;(pdf-Download)&lt;/a&gt;&lt;/h3&gt;\r\n\r\n&lt;p&gt;&lt;em&gt;&lt;strong&gt;Ziele:&lt;/strong&gt;&amp;nbsp;&lt;/em&gt;Perspektivwechsel, erleben von komplexen Themen, entwickeln von Strate gien unter Zeitdruck, Kompromisse erarbeiten&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;em&gt;&lt;strong&gt;Gruppengr&amp;ouml;&amp;szlig;e&lt;/strong&gt;: &lt;/em&gt;F&amp;uuml;r Gro&amp;szlig;gruppen geeignet&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;em&gt;&lt;strong&gt;Zeitbedarf:&lt;/strong&gt;&lt;/em&gt; ca. 30 - 60 Minuten,&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;em&gt;&lt;strong&gt;Materialbedarf:&lt;/strong&gt;&lt;/em&gt; Papier, Stifte, Moderationskarten&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;strong&gt;&lt;em&gt;Ablauf:&lt;/em&gt;&lt;/strong&gt; Zu Beginn erkl&amp;auml;rt die Spielleitung das Ziel des Planspiels, die Spielregeln und das Szenario. M&amp;ouml;gliche Szenarien k&amp;ouml;nnten sein: &amp;raquo;Schulpolitik&amp;laquo;, &amp;raquo;Ausbildungsplatzsituation&amp;laquo; usw. Anschlie&amp;szlig;end werden die Rollen durch die Spielleitung verteilt. Dabei kann eine Rolle durchaus doppelt besetzt werden. Nach einer Verst&amp;auml;ndnisrunde habt Ihr ausreichend Zeit, um Euch in das gew&amp;auml;hlte Szenario und Eure Rollen einzuarbeiten.....&lt;/p&gt;\r\n\r\n&lt;hr style=&quot;border-bottom-width: 1px; border-bottom-style: solid; border-bottom-color: rgb(255, 255, 255); border-top-color: rgba(102, 102, 102, 0.246094); margin: 0px; color: rgb(68, 68, 68); font-family: Arial, sans-serif; &quot; /&gt;\r\n&lt;h3 style=&quot;margin: 0px 0px 0.5em; font-family: Arial, sans-serif; font-weight: bold; line-height: 1.5em; color: rgb(68, 68, 68); text-rendering: optimizelegibility; font-size: 1.1em; &quot;&gt;&lt;a id=&quot;Streit&quot; name=&quot;Streit&quot;&gt;&lt;/a&gt;&lt;u&gt;Das Streitgespr&amp;auml;ch&lt;/u&gt; &lt;a href=&quot;http://tool.ichmache-politik.de/media/misc/9%20Streitgespr%C3%A4ch.pdf&quot; target=&quot;_blank&quot;&gt;(pdf-Download)&lt;/a&gt;&lt;/h3&gt;\r\n\r\n&lt;p&gt;&lt;em&gt;&lt;strong&gt;Ziele:&lt;/strong&gt;&amp;nbsp;&lt;/em&gt;Perspektivwechsel, Problemlagen bewusst machen, L&amp;ouml;sungen entwickeln, Argumente formulieren&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;em&gt;&lt;strong&gt;Gruppengr&amp;ouml;&amp;szlig;e&lt;/strong&gt;: &lt;/em&gt;F&amp;uuml;r Kleingruppen geeignet&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;em&gt;&lt;strong&gt;Zeitbedarf:&lt;/strong&gt;&lt;/em&gt; ca. 30 Minuten,&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;em&gt;&lt;strong&gt;Materialbedarf:&lt;/strong&gt;&lt;/em&gt; Papier, Stifte, Karteikarten&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;strong&gt;&lt;em&gt;Ablauf:&lt;/em&gt;&lt;/strong&gt; Diese Methode eignet sich gut&lt;br /&gt;\r\nals Vorbereitung auf Gespr&amp;auml;che mit Politiker_innen, aber auch um die richtigen und &amp;uuml;berzeugenden Argumente in einer Debatte zu finden. Zun&amp;auml;chst solltet Ihr eine_n Moderator_in f&amp;uuml;r das Streitgespr&amp;auml;ch ausw&amp;auml;hlen. Das Thema kann entweder von dem/der Moderator_ in vorgegeben werden oder Ihr sucht Euch ein Thema aus. Teilt Euch in zwei Gruppen auf, jede Gruppe steht f&amp;uuml;r eine Position. Diskutiert die Frage....&lt;/p&gt;\r\n\r\n&lt;hr style=&quot;border-bottom-width: 1px; border-bottom-style: solid; border-bottom-color: rgb(255, 255, 255); border-top-color: rgba(102, 102, 102, 0.246094); margin: 0px; color: rgb(68, 68, 68); font-family: Arial, sans-serif; &quot; /&gt;\r\n&lt;h3 style=&quot;margin: 0px 0px 0.5em; font-family: Arial, sans-serif; font-weight: bold; line-height: 1.5em; color: rgb(68, 68, 68); text-rendering: optimizelegibility; font-size: 1.1em; &quot;&gt;&lt;a id=&quot;Talkshow&quot; name=&quot;Talkshow&quot;&gt;&lt;/a&gt;&lt;u&gt;Talkshow&lt;/u&gt;&amp;nbsp; &lt;a href=&quot;http://tool.ichmache-politik.de/media/misc/10%20Talkshow.pdf&quot; target=&quot;_blank&quot;&gt;(pdf-Download)&lt;/a&gt;&lt;/h3&gt;\r\n\r\n&lt;p&gt;&lt;em&gt;&lt;strong&gt;Ziele:&lt;/strong&gt;&amp;nbsp;&lt;/em&gt;Perspektivwechsel, Problemlagen bewusst machen, Argumentieren lernen&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;em&gt;&lt;strong&gt;Gruppengr&amp;ouml;&amp;szlig;e&lt;/strong&gt;: &lt;/em&gt;F&amp;uuml;r Klein gruppen geeignet&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;em&gt;&lt;strong&gt;Zeitbedarf:&lt;/strong&gt;&lt;/em&gt; ca. 30 Minuten,&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;em&gt;&lt;strong&gt;Materialbedarf:&lt;/strong&gt;&lt;/em&gt; Papier, Stifte, Karteikarten&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;strong&gt;&lt;em&gt;Ablauf:&lt;/em&gt;&lt;/strong&gt;&amp;nbsp;Die Talkshow-Methode ist oft sehr unterhaltsam. Da die Probleml&amp;ouml;sung und Konsensfindung nicht im Vordergrund steht, eignet sie sich gut als eine Methode, die Projekte abschlie&amp;szlig;t. F&amp;uuml;r die Vorbereitung und Moderation w&amp;auml;hlt Ihr am besten jemanden aus Eurer Gruppe aus. Zun&amp;auml;chst m&amp;uuml;sst Ihr Euch auf ein Thema einigen: Entweder nehmt Ihr ein Thema, was Euch direkt betrifft (Schule, Mitbestimmung) oder eines aus der aktuellen Presse (&amp;raquo;Eurokrise&amp;laquo;, &amp;raquo;Atomausstieg&amp;laquo;). Wichtig f&amp;uuml;r eine Talkshow sind gegens&amp;auml;tzliche Positionen.....&lt;/p&gt;\r\n\r\n&lt;hr style=&quot;border-bottom-width: 1px; border-bottom-style: solid; border-bottom-color: rgb(255, 255, 255); border-top-color: rgba(102, 102, 102, 0.246094); margin: 0px; color: rgb(68, 68, 68); font-family: Arial, sans-serif; &quot; /&gt;\r\n&lt;h3 style=&quot;margin: 0px 0px 0.5em; font-family: Arial, sans-serif; font-weight: bold; line-height: 1.5em; color: rgb(68, 68, 68); text-rendering: optimizelegibility; font-size: 1.1em; &quot;&gt;&lt;a id=&quot;Gesellschaft&quot; name=&quot;Gesellschaft&quot;&gt;&lt;/a&gt;&lt;u&gt;Gesellschaftsbarometer&lt;/u&gt; &lt;a href=&quot;http://tool.ichmache-politik.de/media/misc/11%20Gesellschaftsbarometer.pdf&quot; target=&quot;_blank&quot;&gt;(pdf-Download)&lt;/a&gt;&lt;/h3&gt;\r\n\r\n&lt;p&gt;&lt;em&gt;&lt;strong&gt;Ziele:&lt;/strong&gt;&amp;nbsp;&lt;/em&gt;Perspektivwechsel, die Ungleichheit von gesellschaftlichen Rechten und Chancen und ihre Auswirkungen werden herausgearbeitet&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;em&gt;&lt;strong&gt;Gruppengr&amp;ouml;&amp;szlig;e&lt;/strong&gt;: &lt;/em&gt;F&amp;uuml;r Kleingruppen geeignet (12&amp;ndash;30 Menschen), vorzugsweise R&amp;auml;umlichkeiten mit einem gro&amp;szlig;en Platzangebot&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;em&gt;&lt;strong&gt;Zeitbedarf:&lt;/strong&gt;&lt;/em&gt; ca. 30 - 60 Minuten,&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;em&gt;&lt;strong&gt;Materialbedarf:&lt;/strong&gt;&lt;/em&gt; -&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;strong&gt;&lt;em&gt;Ablauf:&lt;/em&gt;&lt;/strong&gt;&amp;nbsp;F&amp;uuml;r diese Methode solltet Ihr im Vorfeld eine Person ausw&amp;auml;hlen, die sie moderiert und vorbereitet. Als erstes stellt Ihr Euch nebeneinander auf. Alle erhalten ein von dem/der Moderator_in schon vorbereitetes Rollenk&amp;auml;rtchen. In den n&amp;auml;chsten 2 Minuten stellt Ihr Euch innerlich auf die Rollen ein. Ihr k&amp;ouml;nnt den/die Moderator_ in fragen, wenn Euch zur Rolle etwas unklar ist....&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;strong&gt;&lt;a href=&quot;#oben&quot; style=&quot;color: rgb(1, 150, 188); text-decoration: none; &quot;&gt;&amp;gt;&amp;gt; nach oben&lt;/a&gt;&lt;/strong&gt;&lt;/p&gt;\r\n\r\n&lt;hr style=&quot;border-bottom-width: 1px; border-bottom-style: solid; border-bottom-color: rgb(255, 255, 255); border-top-color: rgba(102, 102, 102, 0.246094); margin: 0px; color: rgb(68, 68, 68); font-family: Arial, sans-serif; &quot; /&gt;\r\n&lt;h3 style=&quot;margin: 0px 0px 0.5em; font-family: Arial, sans-serif; font-weight: bold; line-height: 1.5em; color: rgb(68, 68, 68); text-rendering: optimizelegibility; font-size: 1.1em; &quot;&gt;&lt;a id=&quot;Stand&quot; name=&quot;Stand&quot;&gt;&lt;/a&gt;&lt;u&gt;Standbilder&lt;/u&gt;&amp;nbsp; &lt;a href=&quot;http://tool.ichmache-politik.de/media/misc/12%20Standbilder.pdf&quot; target=&quot;_blank&quot;&gt;(pdf-Download)&lt;/a&gt;&lt;/h3&gt;\r\n\r\n&lt;p&gt;&lt;em&gt;&lt;strong&gt;Ziele:&lt;/strong&gt;&amp;nbsp;&lt;/em&gt;Perspektivwechsel, Verbildlichen von Beziehungen/Einstellungen/Gef&amp;uuml;hlen&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;em&gt;&lt;strong&gt;Gruppengr&amp;ouml;&amp;szlig;e&lt;/strong&gt;: &lt;/em&gt;F&amp;uuml;r Kleingruppen geeignet&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;em&gt;&lt;strong&gt;Zeitbedarf:&lt;/strong&gt;&lt;/em&gt; ca. 10 - 30 Minuten,&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;em&gt;&lt;strong&gt;Materialbedarf:&lt;/strong&gt;&lt;/em&gt; -&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;strong&gt;&lt;em&gt;Ablauf: &lt;/em&gt;&lt;/strong&gt;Gemeinsam entscheidet Ihr Euch f&amp;uuml;r ein Thema, dies kann eine aktuelle Fragestellung sein, eine eigene Erfahrung oder eine bestimmte Meinung. Dann w&amp;auml;hlt Ihr ein_e Regisseur_in aus, der/die nach den eigenen Vorstellungen ein Standbild baut. Er/sie w&amp;auml;hlt nach und nach Gruppenteilnehmer_innen aus, die zu seinen/ihren Vorstellungen vom gew&amp;auml;hlten Bild passt. Der/die Regisseur_in stellt die Person in die gew&amp;uuml;nschte Position und verdeutlicht m&amp;ouml;glichst ohne Worte, welche K&amp;ouml;rperhaltung, Gestik oder Mimik jeweils angenommen werden soll.....&lt;/p&gt;\r\n\r\n&lt;hr style=&quot;border-bottom-width: 1px; border-bottom-style: solid; border-bottom-color: rgb(255, 255, 255); border-top-color: rgba(102, 102, 102, 0.246094); margin: 0px; color: rgb(68, 68, 68); font-family: Arial, sans-serif; &quot; /&gt;\r\n&lt;h3 style=&quot;margin: 0px 0px 0.5em; font-family: Arial, sans-serif; font-weight: bold; line-height: 1.5em; color: rgb(68, 68, 68); text-rendering: optimizelegibility; font-size: 1.1em; &quot;&gt;&lt;a id=&quot;Strasse&quot; name=&quot;Strasse&quot;&gt;&lt;/a&gt;&lt;u&gt;Strassenumfrage Interview&lt;/u&gt; &lt;a href=&quot;http://tool.ichmache-politik.de/media/misc/13%20Strassenumfrage%20Interview.pdf&quot; target=&quot;_blank&quot;&gt;(pdf-Download)&lt;/a&gt;&lt;/h3&gt;\r\n\r\n&lt;p&gt;&lt;em&gt;&lt;strong&gt;Ziele:&lt;/strong&gt; &lt;/em&gt;Perspektivwechsel, Informationen sammeln&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;em&gt;&lt;strong&gt;Gruppengr&amp;ouml;&amp;szlig;e&lt;/strong&gt;: &lt;/em&gt;F&amp;uuml;r Kleingruppen (3 &amp;ndash; 6 Menschen) im Freien geeignet&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;em&gt;&lt;strong&gt;Zeitbedarf:&lt;/strong&gt;&lt;/em&gt; ca. 30 Minuten,&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;em&gt;&lt;strong&gt;Materialbedarf:&lt;/strong&gt;&lt;/em&gt; Mikrofon, Aufnahmeger&amp;auml;t, Mut&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;strong&gt;&lt;em&gt;Ablauf:&lt;/em&gt;&lt;/strong&gt; Durch ein Interview oder eine Stra&amp;szlig;enumfrage k&amp;ouml;nnt Ihr Informationen zu einem bestimmten Thema sammeln. So erfahrt Ihr viele unterschiedliche Aspekte eines Themas, auf die Ihr vielleicht von alleine nicht kommt. Zun&amp;auml;chst &amp;uuml;berlegt Ihr Euch, zu welchem Thema Ihr gerne mehr erfahren wollt und was Ihr mit den Informationen machen wollt. Anbieten w&amp;uuml;rde sich zum Beispiel eine Befragung zum Thema: &amp;raquo;Was soll in unserem Ort besser werden?&amp;laquo; Das....&lt;/p&gt;\r\n\r\n&lt;hr style=&quot;border-bottom-width: 1px; border-bottom-style: solid; border-bottom-color: rgb(255, 255, 255); border-top-color: rgba(102, 102, 102, 0.246094); margin: 0px; color: rgb(68, 68, 68); font-family: Arial, sans-serif; &quot; /&gt;\r\n&lt;h3 style=&quot;margin: 0px 0px 0.5em; font-family: Arial, sans-serif; font-weight: bold; line-height: 1.5em; color: rgb(68, 68, 68); text-rendering: optimizelegibility; font-size: 1.1em; &quot;&gt;&lt;a id=&quot;Schlange&quot; name=&quot;Schlange&quot;&gt;&lt;/a&gt;&lt;u&gt;Rollende Infoschlange&lt;/u&gt; &lt;a href=&quot;http://tool.ichmache-politik.de/media/misc/14%20Rollende%20Infoschlange.pdf&quot; target=&quot;_blank&quot;&gt;(pdf-Download)&lt;/a&gt;&lt;/h3&gt;\r\n\r\n&lt;p&gt;&lt;em&gt;&lt;strong&gt;Ziele:&lt;/strong&gt;&amp;nbsp;&lt;/em&gt;Aufmerksamkeit erzielen, Ideen und Bed&amp;uuml;rfnisse von jungen Menschen an die &amp;Ouml;ffentlichkeit bringen&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;em&gt;&lt;strong&gt;Gruppengr&amp;ouml;&amp;szlig;e&lt;/strong&gt;: &lt;/em&gt;F&amp;uuml;r Kleingruppen (4 &amp;ndash; 8 Menschen) im Freien geeignet&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;em&gt;&lt;strong&gt;Zeitbedarf:&lt;/strong&gt;&lt;/em&gt; ca. 10 - 30 Minuten,&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;em&gt;&lt;strong&gt;Materialbedarf:&lt;/strong&gt;&lt;/em&gt; Rollerskates, N&amp;auml;hmaschine, Stoff, Plakate&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;strong&gt;&lt;em&gt;Ablauf:&lt;/em&gt;&lt;/strong&gt;&amp;nbsp;Immer nur mit Plakaten oder Transparenten Eure Meinung auf einer Demo nach au&amp;szlig;en tragen ist auf die Dauer auch &amp;ouml;de. Warum schl&amp;auml;ngelt Ihr Euch nicht einfach in einer langen Schlange auf Rollerblades durch die Demos? Hierf&amp;uuml;r braucht Ihr einen ca. 10 Meter langen Schlauch aus Stoff, in den alle 1,5 Meter oben ein kleines Loch f&amp;uuml;r den Kopf und unten ein gro&amp;szlig;es Loch f&amp;uuml;r die Beine geschnitten und dann ums&amp;auml;umt wird. Eure Botschaft schreibt Ihr auf Plakate und....&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;strong&gt;&lt;a href=&quot;#oben&quot; style=&quot;color: rgb(1, 150, 188); text-decoration: none; &quot;&gt;&amp;gt;&amp;gt; nach oben&lt;/a&gt;&lt;/strong&gt;&lt;/p&gt;\r\n\r\n&lt;hr style=&quot;border-bottom-width: 1px; border-bottom-style: solid; border-bottom-color: rgb(255, 255, 255); border-top-color: rgba(102, 102, 102, 0.246094); margin: 0px; color: rgb(68, 68, 68); font-family: Arial, sans-serif; &quot; /&gt;\r\n&lt;h3 style=&quot;margin: 0px 0px 0.5em; font-family: Arial, sans-serif; font-weight: bold; line-height: 1.5em; color: rgb(68, 68, 68); text-rendering: optimizelegibility; font-size: 1.1em; &quot;&gt;&lt;a id=&quot;Baum&quot; name=&quot;Baum&quot;&gt;&lt;/a&gt;&lt;u&gt;Wunschbaum&lt;/u&gt; &lt;a href=&quot;http://tool.ichmache-politik.de/media/misc/6-3-5%20Methode.pdf&quot; target=&quot;_blank&quot;&gt; &lt;/a&gt;&lt;a href=&quot;http://tool.ichmache-politik.de/media/misc/15%20Wunschbaum.pdf&quot; target=&quot;_blank&quot;&gt;(pdf-Download)&lt;/a&gt;&lt;/h3&gt;\r\n\r\n&lt;p&gt;&lt;em&gt;&lt;strong&gt;Ziele:&lt;/strong&gt; &lt;/em&gt;Aufmerksamkeit erzielen, Ideen und Bed&amp;uuml;rfnisse von jungen Menschen an die &amp;Ouml;ffentlichkeit bringen&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;em&gt;&lt;strong&gt;Gruppengr&amp;ouml;&amp;szlig;e&lt;/strong&gt;: &lt;/em&gt;F&amp;uuml;r jede Gruppengr&amp;ouml;&amp;szlig;e geeignet&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;em&gt;&lt;strong&gt;Zeitbedarf:&lt;/strong&gt;&lt;/em&gt; ca. 60 Minuten,&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;em&gt;&lt;strong&gt;Materialbedarf:&lt;/strong&gt;&lt;/em&gt; ein kleiner Baum, Leiter, Karten, Stifte, F&amp;auml;den, Tisch, Flugbl&amp;auml;tter&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;strong&gt;&lt;em&gt;Ablauf:&lt;/em&gt;&lt;/strong&gt;&amp;nbsp;Diese Methode eignet sich sehr gut, wenn Ihr auf Probleme z. B. in Eurem Stadtteil aufmerksam machen wollt. Hierzu stellt Ihr einen Baum beispielsweise vor dem Rathaus auf und malt und schreibt auf Karten, was Euch in Eurem Stadtteil st&amp;ouml;rt und welche Ideen und W&amp;uuml;nsche Ihr habt, um die Situation vor Ort zu verbessern. Nutzt daf&amp;uuml;r entweder verschiedenfarbige K&amp;auml;rtchen (z.B. rot: was Euch st&amp;ouml;rt, gr&amp;uuml;n: was Ihr Euch w&amp;uuml;nscht) oder bemalt die Vorderseite mit dem Problem....&lt;/p&gt;\r\n\r\n&lt;hr style=&quot;border-bottom-width: 1px; border-bottom-style: solid; border-bottom-color: rgb(255, 255, 255); border-top-color: rgba(102, 102, 102, 0.246094); margin: 0px; color: rgb(68, 68, 68); font-family: Arial, sans-serif; &quot; /&gt;\r\n&lt;h3 style=&quot;margin: 0px 0px 0.5em; font-family: Arial, sans-serif; font-weight: bold; line-height: 1.5em; color: rgb(68, 68, 68); text-rendering: optimizelegibility; font-size: 1.1em; &quot;&gt;&lt;a id=&quot;Stadtteil&quot; name=&quot;Stadtteil&quot;&gt;&lt;/a&gt;&lt;u&gt;Stadtteilpl&amp;auml;ne&lt;/u&gt;&amp;nbsp; &lt;a href=&quot;http://tool.ichmache-politik.de/media/misc/16%20Stadtteilpl%C3%A4ne.pdf&quot; target=&quot;_blank&quot;&gt;(pdf-Download)&lt;/a&gt;&lt;/h3&gt;\r\n\r\n&lt;p&gt;&lt;em&gt;&lt;strong&gt;Ziele:&lt;/strong&gt;&amp;nbsp;&lt;/em&gt;Aufmerksamkeit erzielen, Ideen und Bed&amp;uuml;rfnisse von jungen Menschen an die &amp;Ouml;ffentlichkeit bringen, Erkundung des Stadtteils&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;em&gt;&lt;strong&gt;Gruppengr&amp;ouml;&amp;szlig;e&lt;/strong&gt;: &lt;/em&gt;F&amp;uuml;r jede Gruppengr&amp;ouml;&amp;szlig;e geeignet&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;em&gt;&lt;strong&gt;Zeitbedarf:&lt;/strong&gt;&lt;/em&gt; ca. 30 - 60 Minuten,&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;em&gt;&lt;strong&gt;Materialbedarf:&lt;/strong&gt;&lt;/em&gt; Stadtplan, Zettel, Stifte&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;strong&gt;&lt;em&gt;Ablauf:&lt;/em&gt;&lt;/strong&gt;&amp;nbsp;Zu Beginn versucht Ihr herauszufinden, an welchen Stellen auf dem Stadtplan Orte eingezeichnet sind, die f&amp;uuml;r Euch interessant sind: Spielpl&amp;auml;tze, Jugendzentren, Sportpl&amp;auml;tze, Freifl&amp;auml;chen, &amp;hellip; Diese Stellen werden markiert. Nun &amp;uuml;berlegt Ihr Euch, was Ihr schon &amp;uuml;ber die markierten Pl&amp;auml;tze wisst. Wer hat welche Informationen &amp;uuml;ber die markierten Pl&amp;auml;tze? Was passiert dort? Kosten die Angebote Geld? Diese Informationen k&amp;ouml;nnt ihr auf die bereitgelegten Zettel aufschreiben und anschlie&amp;szlig;end markiert Ihr...&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;strong&gt;&lt;a href=&quot;#oben&quot; style=&quot;color: rgb(1, 150, 188); text-decoration: none; &quot;&gt;&amp;gt;&amp;gt; nach oben&lt;/a&gt;&lt;/strong&gt;&lt;/p&gt;\r\n\r\n&lt;hr style=&quot;border-bottom-width: 1px; border-bottom-style: solid; border-bottom-color: rgb(255, 255, 255); border-top-color: rgba(102, 102, 102, 0.246094); margin: 0px; color: rgb(68, 68, 68); font-family: Arial, sans-serif; &quot; /&gt;\r\n&lt;h3 style=&quot;margin: 0px 0px 0.5em; font-family: Arial, sans-serif; font-weight: bold; line-height: 1.5em; color: rgb(68, 68, 68); text-rendering: optimizelegibility; font-size: 1.1em; &quot;&gt;&lt;a id=&quot;Fishbowl&quot; name=&quot;Fishbowl&quot;&gt;&lt;/a&gt;&lt;u&gt;Fishbowl&lt;/u&gt;&amp;nbsp; &lt;a href=&quot;http://tool.ichmache-politik.de/media/misc/17%20Fishbowl.pdf&quot; target=&quot;_blank&quot;&gt;(pdf-Download)&lt;/a&gt;&lt;/h3&gt;\r\n\r\n&lt;p&gt;&lt;em&gt;&lt;strong&gt;Ziele:&lt;/strong&gt;&amp;nbsp;&lt;/em&gt;Gemeinsame, gleichberechtigte Auseinandersetzung mit einem Thema, dynamischer Diskussionsablauf ohne Redeliste&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;em&gt;&lt;strong&gt;Gruppengr&amp;ouml;&amp;szlig;e&lt;/strong&gt;: &lt;/em&gt;F&amp;uuml;r Klein- und Gro&amp;szlig;gruppen geeignet (10 &amp;ndash; 100 Menschen)&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;em&gt;&lt;strong&gt;Zeitbedarf:&lt;/strong&gt;&lt;/em&gt; ca. 30 - 120 Minuten,&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;em&gt;&lt;strong&gt;Materialbedarf:&lt;/strong&gt;&lt;/em&gt; ein Tisch mit 6 St&amp;uuml;hlen in der Mitte des Raumes, kreisf&amp;ouml;rmig andere St&amp;uuml;hle drum herum&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;strong&gt;&lt;em&gt;Ablauf:&lt;/em&gt;&lt;/strong&gt;&amp;nbsp;Ihr m&amp;uuml;sst einen Innenkreis (&amp;raquo;Goldfisch-Glas&amp;laquo;) aus den sechs St&amp;uuml;hlen bilden, die restlichen St&amp;uuml;hle werden im Kreis um das &amp;raquo;Glas&amp;laquo; angeordnet. Bei der Fishbowl-Methode diskutiert die kleinere Gruppe von Teilnehmer_innen (f&amp;uuml;nf) im Innenkreis ein Thema. Die &amp;uuml;brigen Teilnehmer_innen beobachten die Diskussion vom Au&amp;szlig;enkreis aus. Das Besondere ist, dass&amp;nbsp;im Innenkreis immer ein Stuhl frei gelassen wird, so dass Teilnehmer_innen aus dem Au&amp;szlig;enkreis jederzeit in den Innenkreis wechseln k&amp;ouml;nnen ...&lt;/p&gt;\r\n\r\n&lt;hr style=&quot;border-bottom-width: 1px; border-bottom-style: solid; border-bottom-color: rgb(255, 255, 255); border-top-color: rgba(102, 102, 102, 0.246094); margin: 0px; color: rgb(68, 68, 68); font-family: Arial, sans-serif; &quot; /&gt;\r\n&lt;h3 style=&quot;margin: 0px 0px 0.5em; font-family: Arial, sans-serif; font-weight: bold; line-height: 1.5em; color: rgb(68, 68, 68); text-rendering: optimizelegibility; font-size: 1.1em; &quot;&gt;&lt;a id=&quot;Open&quot; name=&quot;Open&quot;&gt;&lt;/a&gt;&lt;u&gt;Open Space&lt;/u&gt; &lt;a href=&quot;http://tool.ichmache-politik.de/media/misc/18%20open%20space.pdf&quot; target=&quot;_blank&quot;&gt;(pdf-Download)&lt;/a&gt;&lt;/h3&gt;\r\n\r\n&lt;p&gt;&lt;em&gt;&lt;strong&gt;Ziele:&lt;/strong&gt;&amp;nbsp;&lt;/em&gt;Gemeinsame, gleichberechtigte Auseinandersetzung mit einem Thema, alle Teilnehmer_innen gestalten die Inhalte und Methoden selbstverantwortlich mit&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;em&gt;&lt;strong&gt;Gruppengr&amp;ouml;&amp;szlig;e&lt;/strong&gt;: &lt;/em&gt;F&amp;uuml;r Klein-und Gro&amp;szlig;gruppen geeignet (12 &amp;ndash; 100 Menschen)&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;em&gt;&lt;strong&gt;Zeitbedarf:&lt;/strong&gt;&lt;/em&gt; ca. 30 - 120 Minuten&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;em&gt;&lt;strong&gt;Materialbedarf:&lt;/strong&gt;&lt;/em&gt; R&amp;auml;umlichkeiten, Moderationsmaterial&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;strong&gt;&lt;em&gt;Ablauf:&lt;/em&gt;&lt;/strong&gt;&amp;nbsp;Open Space ist eine Methode f&amp;uuml;r die Gestaltung von gro&amp;szlig;en Seminaren, Versammlungen oder Treffen zur Planung konkreter Projekte. Sie bedarf einer guten Vor bereitung. Am Anfang sitzt Ihr alle in einem Kreis. Nach einer knappen Einf&amp;uuml;hrung in die Grunds&amp;auml;tze und Regeln von Open Space habt Ihr die M&amp;ouml;glichkeit, in die Mitte des Kreises zu gehen und Eure Anliegen zu nennen. Etwas, das Euch unter den N&amp;auml;geln brennt, am ...&lt;/p&gt;\r\n\r\n&lt;hr style=&quot;border-bottom-width: 1px; border-bottom-style: solid; border-bottom-color: rgb(255, 255, 255); border-top-color: rgba(102, 102, 102, 0.246094); margin: 0px; color: rgb(68, 68, 68); font-family: Arial, sans-serif; &quot; /&gt;\r\n&lt;h3 style=&quot;margin: 0px 0px 0.5em; font-family: Arial, sans-serif; font-weight: bold; line-height: 1.5em; color: rgb(68, 68, 68); text-rendering: optimizelegibility; font-size: 1.1em; &quot;&gt;&lt;a id=&quot;Blitz&quot; name=&quot;Blitz&quot;&gt;&lt;/a&gt;&lt;u&gt;Blitzlicht&lt;/u&gt;&amp;nbsp; &lt;a href=&quot;http://tool.ichmache-politik.de/media/misc/19%20Blitz%20licht.pdf&quot; target=&quot;_blank&quot;&gt;(pdf-Download)&lt;/a&gt;&lt;/h3&gt;\r\n\r\n&lt;p&gt;&lt;em&gt;&lt;strong&gt;Ziele:&lt;/strong&gt;&amp;nbsp;&lt;/em&gt;Feedback, Stimmungs- und Meinungsbilder einfangen&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;em&gt;&lt;strong&gt;Gruppengr&amp;ouml;&amp;szlig;e&lt;/strong&gt;: &lt;/em&gt;F&amp;uuml;r Kleingruppen (5&amp;ndash;25 Menschen) geeignet&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;em&gt;&lt;strong&gt;Zeitbedarf:&lt;/strong&gt;&lt;/em&gt; ca. 10 - 30 Minuten,&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;em&gt;&lt;strong&gt;Materialbedarf:&lt;/strong&gt;&lt;/em&gt; Ein kleiner Gegenstand als Sprechstein&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;strong&gt;&lt;em&gt;Ablauf:&lt;/em&gt;&lt;/strong&gt;&amp;nbsp;Die Blitzlichtmethode eignet sich gut als Feedback-Methode, so k&amp;ouml;nnt Ihr schnell die Stimmungen, Meinungen oder den Stand bez&amp;uuml;glich der Inhalte und Beziehungen in Eurer Gruppe ermitteln. Am besten setzt Ihr Euch in einen Kreis. Der- oder diejenige mit dem &amp;raquo;Sprechstein&amp;laquo; in der Hand, kann in ein bis zwei S&amp;auml;tzen, aber maximal eine Minute, Stellung zu einer von Euch im Vorfeld aus gew&amp;auml;hlten Frage beziehen. Hierbei solltet Ihr Euch an folgende Regeln halten,damit die Methode auch funktioniert: ....&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;strong&gt;&lt;a href=&quot;#oben&quot; style=&quot;color: rgb(1, 150, 188); text-decoration: none; &quot;&gt;&amp;gt;&amp;gt; nach oben&lt;/a&gt;&lt;/strong&gt;&lt;/p&gt;\r\n\r\n&lt;hr style=&quot;border-bottom-width: 1px; border-bottom-style: solid; border-bottom-color: rgb(255, 255, 255); border-top-color: rgba(102, 102, 102, 0.246094); margin: 0px; color: rgb(68, 68, 68); font-family: Arial, sans-serif; &quot; /&gt;\r\n&lt;h1&gt;&lt;strong&gt;&lt;a id=&quot;Digital&quot; name=&quot;Digital&quot;&gt;&lt;/a&gt;&lt;/strong&gt;&lt;/h1&gt;\r\n\r\n&lt;h1&gt;&lt;strong&gt;Methoden digital&lt;/strong&gt;&lt;/h1&gt;\r\n\r\n&lt;h3&gt;Hilfreiche Tools aus dem digitalen Bereich:&lt;/h3&gt;\r\n\r\n&lt;p&gt;&lt;strong&gt;&lt;a href=&quot;http://www.diigo.com/&quot; style=&quot;color: rgb(1, 150, 188); text-decoration: none; &quot; target=&quot;_blank&quot;&gt;&amp;gt;&amp;gt; Diigo&lt;/a&gt;&lt;/strong&gt;: Das Tool Diego erm&amp;ouml;glicht es Online-Texte, wie Texte auf Papier zu behandeln. So kann man Abschnitte hervorheben, eigene Notizen anf&amp;uuml;gen oder Stichw&amp;ouml;rter vergeben. Optimal also f&amp;uuml;r die erste Online-Recherche zum jeweiligen Thema!&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;strong&gt;&lt;a href=&quot;http://prezi.com/&quot; style=&quot;color: rgb(1, 150, 188); text-decoration: none; &quot; target=&quot;_blank&quot;&gt;&amp;gt;&amp;gt; Prezi&lt;/a&gt;&lt;/strong&gt;: Prezi ist ein Pr&amp;auml;sentationsprogramm, dass eine sehr dynamische Darstellung von Inhalten erm&amp;ouml;glicht. Man kann sich das Programm als ein unendlich gro&amp;szlig;es Flipchart-Papier vorstellen, auf welchem man sich per Mausklick bewegen kann. Au&amp;szlig;erdem erm&amp;ouml;glicht es ein hinein- und hinauszoomen, was eine sehr tiefgehende Vermittlung von Inhalten erm&amp;ouml;glicht. Es erfordert ein wenig Zeit sich in das Tool hineinzudenken, doch es lohnt sich!&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;strong&gt;&lt;a href=&quot;http://animoto.com/&quot; style=&quot;color: rgb(1, 150, 188); text-decoration: none; &quot; target=&quot;_blank&quot;&gt;&amp;gt;&amp;gt; Animoto&lt;/a&gt;&lt;/strong&gt;: Das Online-Tool animoto erstellt wie von Zauberhand hochaufl&amp;ouml;sende Videos. Der Anwender liefert lediglich das Tonmaterial, Bilder oder Grafiken und wenn man will noch ain paar kleinere Texte. Animoto stellt diese zu einer &amp;quot;Slideshow&amp;quot; zusammen. Die kostenlose Lite-Variante bastelt Video&amp;acute;s mit einer maximalen L&amp;auml;nge von 30 Sekunden.&lt;/p&gt;\r\n\r\n&lt;hr style=&quot;margin: 0px; border-bottom-width: 1px; border-top-color: rgba(102, 102, 102, 0.246094); border-bottom-style: solid; border-bottom-color: rgb(255, 255, 255); color: rgb(68, 68, 68); font-family: Arial, sans-serif; &quot; /&gt;\r\n&lt;h4 style=&quot;margin: 0px 0px 0.5em; font-family: Arial, sans-serif; font-weight: bold; line-height: 20px; color: rgb(68, 68, 68); text-rendering: optimizelegibility; &quot;&gt;Schonmal was vom Educaching geh&amp;ouml;rt?&lt;strong&gt;&lt;a href=&quot;http://pb21.de/2010/11/educaching-lernen-wie-im-echten-leben-ii/&quot; style=&quot;color: rgb(1, 150, 188); text-decoration: none; &quot; target=&quot;_blank&quot;&gt;&amp;nbsp;&amp;gt;&amp;gt; Hier erfahrt ihr mehr!&lt;/a&gt;&lt;/strong&gt;&lt;/h4&gt;\r\n', '&lt;h4 style=&quot;margin: 0px 0px 0.5em; font-family: Arial,sans-serif; font-weight: bold; line-height: 1.5em; color: rgb(68, 68, 68); text-rendering: optimizelegibility; font-size: 1.1em;&quot;&gt;&lt;a href=&quot;#Brainstorming&quot;&gt;Brainstorming&lt;/a&gt;&lt;/h4&gt;\r\n\r\n&lt;h3 style=&quot;margin: 0px 0px 0.5em; font-family: Arial, sans-serif; font-weight: bold; line-height: 1.5em; color: rgb(68, 68, 68); text-rendering: optimizelegibility; font-size: 1.1em; &quot;&gt;&lt;a href=&quot;#Kopf&quot;&gt;Kopfstand-Methode&lt;/a&gt;&lt;/h3&gt;\r\n\r\n&lt;h3 style=&quot;margin: 0px 0px 0.5em; font-family: Arial, sans-serif; font-weight: bold; line-height: 1.5em; color: rgb(68, 68, 68); text-rendering: optimizelegibility; font-size: 1.1em; &quot;&gt;&lt;a href=&quot;#Kugel&quot;&gt;Kugellager&lt;/a&gt;&lt;/h3&gt;\r\n\r\n&lt;h3 style=&quot;margin: 0px 0px 0.5em; font-family: Arial, sans-serif; font-weight: bold; line-height: 1.5em; color: rgb(68, 68, 68); text-rendering: optimizelegibility; font-size: 1.1em; &quot;&gt;&lt;a href=&quot;#6-3-5&quot;&gt;6-3-5 Methode&lt;/a&gt;&lt;/h3&gt;\r\n\r\n&lt;h3 style=&quot;margin: 0px 0px 0.5em; font-family: Arial, sans-serif; font-weight: bold; line-height: 1.5em; color: rgb(68, 68, 68); text-rendering: optimizelegibility; font-size: 1.1em; &quot;&gt;&lt;a href=&quot;#Mind&quot;&gt;Mindmapping&lt;/a&gt;&lt;/h3&gt;\r\n\r\n&lt;h3 style=&quot;margin: 0px 0px 0.5em; font-family: Arial, sans-serif; font-weight: bold; line-height: 1.5em; color: rgb(68, 68, 68); text-rendering: optimizelegibility; font-size: 1.1em; &quot;&gt;&lt;a href=&quot;#Stein&quot;&gt;Stolpersteine&lt;/a&gt;&lt;/h3&gt;\r\n\r\n&lt;h3 style=&quot;margin: 0px 0px 0.5em; font-family: Arial, sans-serif; font-weight: bold; line-height: 1.5em; color: rgb(68, 68, 68); text-rendering: optimizelegibility; font-size: 1.1em; &quot;&gt;&lt;a href=&quot;#Bord&quot;&gt;Alles an Bord&lt;/a&gt;&lt;/h3&gt;\r\n\r\n&lt;h3 style=&quot;margin: 0px 0px 0.5em; font-family: Arial, sans-serif; font-weight: bold; line-height: 1.5em; color: rgb(68, 68, 68); text-rendering: optimizelegibility; font-size: 1.1em; &quot;&gt;&lt;a href=&quot;#Plan&quot;&gt;Das Planspiel&lt;/a&gt;&lt;/h3&gt;\r\n\r\n&lt;h3 style=&quot;margin: 0px 0px 0.5em; font-family: Arial, sans-serif; font-weight: bold; line-height: 1.5em; color: rgb(68, 68, 68); text-rendering: optimizelegibility; font-size: 1.1em; &quot;&gt;&lt;a href=&quot;#Streit&quot;&gt;Das Streitgespr&amp;auml;ch&lt;/a&gt;&lt;/h3&gt;\r\n\r\n&lt;h3&gt;&lt;strong&gt;&lt;a href=&quot;#Talkshow&quot;&gt;Talkshow&lt;/a&gt;&lt;/strong&gt;&lt;/h3&gt;\r\n\r\n&lt;h3&gt;&lt;strong&gt;&lt;a href=&quot;#Gesellschaft&quot;&gt;Gesellschaftsbarometer&lt;/a&gt;&lt;/strong&gt;&lt;/h3&gt;\r\n\r\n&lt;h3&gt;&lt;a href=&quot;#Stand&quot;&gt;&lt;strong&gt;Standbilder&lt;/strong&gt;&lt;/a&gt;&lt;/h3&gt;\r\n\r\n&lt;h3&gt;&lt;a href=&quot;#Strasse&quot;&gt;&lt;strong&gt;Strassenumfrage Interview&lt;/strong&gt;&lt;/a&gt;&lt;/h3&gt;\r\n\r\n&lt;h3&gt;&lt;a href=&quot;#Schlange&quot;&gt;&lt;strong&gt;Rollende Infoschlange&lt;/strong&gt;&lt;/a&gt;&lt;/h3&gt;\r\n\r\n&lt;h3&gt;&lt;a href=&quot;#Baum&quot;&gt;&lt;strong&gt;Wunschbaum&lt;/strong&gt;&lt;/a&gt;&lt;/h3&gt;\r\n\r\n&lt;h3&gt;&lt;a href=&quot;#Stadtteil&quot;&gt;&lt;strong&gt;Stadtteilpl&amp;auml;ne&lt;/strong&gt;&lt;/a&gt;&lt;/h3&gt;\r\n\r\n&lt;h3&gt;&lt;a href=&quot;#Fishbowl&quot;&gt;&lt;strong&gt;Fishbowl&lt;/strong&gt;&lt;/a&gt;&lt;/h3&gt;\r\n\r\n&lt;h3&gt;&lt;a href=&quot;#Open&quot;&gt;&lt;strong&gt;Open Space&lt;/strong&gt;&lt;/a&gt;&lt;/h3&gt;\r\n\r\n&lt;h3&gt;&lt;a href=&quot;#Blitz&quot;&gt;&lt;strong&gt;Blitzlicht&lt;/strong&gt;&lt;/a&gt;&lt;/h3&gt;\r\n\r\n&lt;h3&gt;&lt;a href=&quot;#Digital&quot;&gt;&lt;strong&gt;Methoden digital&lt;/strong&gt;&lt;/a&gt;&lt;/h3&gt;\r\n', 169),
(174, 1, 'xx', 'Infos zu Frage 2', 'n', 'cnslt_backgr', '&lt;h1&gt;&lt;strong&gt;Infos zu Frage 2&lt;/strong&gt;&lt;/h1&gt;\r\n\r\n&lt;p&gt;Hier k&amp;ouml;nnt ihr alle wichtigen Hintergrundeinformationen zu dieser Frage einstellen: Texte, Artikel, Bilder, Videos und weiterf&amp;uuml;hrende Links, d&amp;uuml;rfen nat&amp;uuml;rlich auch nicht fehlen.&lt;/p&gt;\r\n', '', 168),
(175, 1, 'xx', 'Infos zu Frage 3', 'n', 'cnslt_backgr', '&lt;h1&gt;&lt;strong&gt;Infos zu Frage 3&lt;/strong&gt;&lt;/h1&gt;\r\n\r\n&lt;p&gt;Hier k&amp;ouml;nnt ihr alle wichtigen Hintergrundeinformationen zu dieser Frage einstellen: Texte, Artikel, Bilder, Videos und weiterf&amp;uuml;hrende Links, d&amp;uuml;rfen nat&amp;uuml;rlich auch nicht fehlen.&lt;/p&gt;\r\n', '', 168);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `articles_refnm`
--

CREATE TABLE IF NOT EXISTS `articles_refnm` (
  `ref_nm` varchar(30) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'article reference name',
  `lng` char(2) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'language code',
  `desc` varchar(44) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'readable description',
  `type` enum('g','b','s','m') COLLATE utf8_unicode_ci NOT NULL DEFAULT 's' COMMENT 'general, basic, specific, mail',
  `scope` enum('none','info','voting','followup','static') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'none' COMMENT 'none, info, voting, followup, static',
  UNIQUE KEY `ref_nm` (`ref_nm`,`lng`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='default list of article types';

--
-- Daten für Tabelle `articles_refnm`
--

INSERT INTO `articles_refnm` (`ref_nm`, `lng`, `desc`, `type`, `scope`) VALUES
('cnslt_backgr', 'de', 'Infos zum Thema', 'b', 'info'),
('cnslt_help', 'de', 'Praxishilfen', 'b', 'none'),
('cnslt_info', 'de', 'Infos zum Verfahren', 'b', 'info'),
('cnslt_quest', 'de', 'Fragenübersicht', 'b', 'info'),
('cnslt_summ', 'de', 'Zusammenfassung', 'b', 'none'),
('contact', 'de', 'Kontakt', 'g', 'static'),
('faq', 'de', 'Häufige Fragen', 'g', 'static'),
('followup', 'de', 'Follow-up', 'b', 'followup'),
('privacy', 'de', 'Datenschutz', 'g', 'static'),
('vot_res', 'de', 'Erklärung Abstimmungsergebnisse', 'b', 'voting'),
('about', 'de', 'Über uns', 'g', 'static'),
('imprint', 'de', 'Impressum', 'g', 'static'),
('vot_res_cnslt', 'de', 'Erklärung Abstimmungsergebnisse', 'b', 'voting');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `cnslt`
--

CREATE TABLE IF NOT EXISTS `cnslt` (
  `kid` smallint(5) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Consultation ID',
  `proj` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'gehört zu SD oder zur eigst Jugpol',
  `inp_fr` datetime NOT NULL COMMENT 'Input possible from date on',
  `inp_to` datetime NOT NULL COMMENT 'Input possible till',
  `inp_show` enum('y','n') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'y' COMMENT 'Show input period',
  `spprt_show` enum('y','n') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n' COMMENT 'Show support button',
  `spprt_fr` datetime NOT NULL COMMENT 'support button clickable from',
  `spprt_to` datetime NOT NULL COMMENT 'Supporting possible until',
  `spprt_ct` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Counter for accumulated supports',
  `disc_show` enum('y','n') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n' COMMENT 'Show discussion button',
  `vot_fr` datetime NOT NULL COMMENT 'Voting possible from date on',
  `vot_to` datetime NOT NULL COMMENT 'Voting possible till',
  `vot_show` enum('y','n') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'y' COMMENT 'Show voting period',
  `vot_expl` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'info text for voting start',
  `vot_res_show` enum('y','n') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n' COMMENT 'Show voting results',
  `summ_show` enum('y','n') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n' COMMENT 'show summary of voting',
  `follup_show` enum('y','n') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n' COMMENT 'show follow-up',
  `ord` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'order in slider (the higher the more important)',
  `titl` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Title of consultation',
  `titl_short` varchar(40) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Shortened title (for slider, mails etc.)',
  `titl_sub` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'subtitle (optional)',
  `img_file` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'file name of title graphics',
  `img_expl` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'explanatory text for title graphics',
  `expl` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'Explanatory text',
  `expl_short` text COLLATE utf8_unicode_ci NOT NULL,
  `ln` char(2) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Language',
  `adm` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT 'Administrated by [not used yet]',
  `public` enum('y','n') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n' COMMENT 'y=visible to public',
  `is_discussion_active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`kid`) USING BTREE
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AVG_ROW_LENGTH=217 CHECKSUM=1 COMMENT='Basic definitions of consultations' AUTO_INCREMENT=21 ;

--
-- Daten für Tabelle `cnslt`
--

INSERT INTO `cnslt` (`kid`, `proj`, `inp_fr`, `inp_to`, `inp_show`, `spprt_show`, `spprt_fr`, `spprt_to`, `spprt_ct`, `disc_show`, `vot_fr`, `vot_to`, `vot_show`, `vot_expl`, `vot_res_show`, `summ_show`, `follup_show`, `ord`, `titl`, `titl_short`, `titl_sub`, `img_file`, `img_expl`, `expl`, `expl_short`, `ln`, `adm`, `public`, `is_discussion_active`) VALUES
(1, 'xx', '2014-01-01 00:00:00', '2014-12-31 23:59:59', 'y', 'n', '2014-01-01 00:00:00', '2014-12-31 23:59:59', 0, 'n', '2011-07-18 23:59:00', '2011-07-18 23:59:00', 'n', '', 'n', 'y', 'y', 1, 'Unsere erste Beteiligungsrunde', 'Hier kommt der Kurztitel hin!', 'Denkt euch einen Titel aus!', 'jugendliche_im_gras.jpg', 'Foto: Fotolia © Godfer', '&lt;h1&gt;Darum ging&amp;#39;s&lt;/h1&gt;\r\n\r\n&lt;p&gt;Die Seite &amp;quot;Infos&amp;quot; ist der erste Zugang der Teilnehmenden zu eurer Runde. Deshalb solltet ihr hier beschreiben worum es geht und warum euch das jeweilige Thema wichtig ist. Das ganze k&amp;ouml;nnt ihr dann noch mit Bilder, Clips oder sonstigen Materialien anreichern. Aber ihr wisst sicher am Besten, welche Informationen f&amp;uuml;r die Teilnehmenden wichtig sind, also: LOS GEHT&amp;#39;S!&lt;/p&gt;\r\n', '&lt;p&gt;&lt;em&gt;In diese Box schreibt ihr eine kurze Beschreibung eurer Beteiligungsrunde. Z.B. die Hauptfrage oder einige Worte zum Thema. Achtet darauf euch auf einige wenige S&auml;tze zu beschr&auml;nken, damit nichts abgeschnitten wird :-) &lt;em&gt;', 'de', 22, 'y', 0);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `dirs`
--

CREATE TABLE IF NOT EXISTS `dirs` (
  `id` int(12) unsigned NOT NULL AUTO_INCREMENT,
  `kid` int(12) NOT NULL,
  `dir_name` varchar(120) COLLATE utf8_bin NOT NULL,
  `left` int(12) unsigned NOT NULL,
  `right` int(12) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `lft` (`left`),
  KEY `rgt` (`right`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='voting prep folders (nested sets)' AUTO_INCREMENT=32 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `discuss`
--

CREATE TABLE IF NOT EXISTS `discuss` (
  `tid` int(10) unsigned NOT NULL,
  `uid` mediumint(8) unsigned NOT NULL,
  `tmphash` char(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`tid`,`uid`,`tmphash`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=FIXED COMMENT='Zähler für Diskussionsbedarf-Button';

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `discussns`
--

CREATE TABLE IF NOT EXISTS `discussns` (
  `tid` int(10) unsigned NOT NULL COMMENT 'ThesenID',
  `uid` int(10) unsigned NOT NULL COMMENT 'UserID',
  `when` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'When written',
  `inpt` varchar(6000) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Actual text',
  `type` enum('i','r','d','n','c','s') COLLATE utf8_unicode_ci NOT NULL COMMENT 'initial copy of tid, revision of tid, default, new tid, closed thread, secret',
  UNIQUE KEY `tid` (`tid`,`when`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Discussion threads';

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `edt_cnslt`
--

CREATE TABLE IF NOT EXISTS `edt_cnslt` (
  `uid` mediumint(8) unsigned NOT NULL COMMENT 'ref to UserID of editor',
  `kid` smallint(5) unsigned NOT NULL COMMENT 'ref to KID',
  `edt` enum('y','n') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'y' COMMENT 'right to edit',
  `own` enum('y','n') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n' COMMENT 'is owner of this cnslt',
  PRIMARY KEY (`uid`,`kid`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci CHECKSUM=1 ROW_FORMAT=FIXED COMMENT='defines rights of editors or admins related to consultations';

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `fowups`
--

CREATE TABLE IF NOT EXISTS `fowups` (
  `fid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Follow-up ID',
  `docorg` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'internal order of document',
  `embed` varchar(600) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'embedding for multimedia',
  `expl` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'Erläuterung',
  `typ` enum('g','s','a','r','e') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'g' COMMENT 'general, supporting, action, rejected, end',
  `ffid` smallint(5) unsigned zerofill NOT NULL COMMENT 'reference to Follow-up File ID',
  `hlvl` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT 'hierarchy level in document, 1 is standard text,0 is footnoote, >1 are headings',
  `lkyea` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT 'number of likes',
  `lknay` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT 'number of dislikes',
  PRIMARY KEY (`fid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `fowups_rid`
--

CREATE TABLE IF NOT EXISTS `fowups_rid` (
  `fid_ref` int(10) NOT NULL,
  `fid` int(10) NOT NULL DEFAULT '0' COMMENT 'fk: fowups.fid',
  `tid` int(10) NOT NULL DEFAULT '0' COMMENT 'fk: inpt.tid',
  `ffid` int(10) NOT NULL DEFAULT '0' COMMENT 'fk: fowup_fls.ffid',
  PRIMARY KEY (`fid_ref`,`fid`,`tid`,`ffid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `fowups_supports`
--

CREATE TABLE IF NOT EXISTS `fowups_supports` (
  `fid` int(10) NOT NULL,
  `tmphash` char(32) NOT NULL,
  PRIMARY KEY (`fid`,`tmphash`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `fowup_fls`
--

CREATE TABLE IF NOT EXISTS `fowup_fls` (
  `ffid` smallint(5) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Follow-up File ID',
  `kid` smallint(5) unsigned NOT NULL COMMENT 'ref to KonsultationID',
  `titl` varchar(300) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Title of follow-up document',
  `who` varchar(200) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Who gave the follow-up',
  `when` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'When was it released',
  `show_no_day` enum('n','y') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n' COMMENT 'cell ''when'' shown only as year and month',
  `ref_doc` varchar(160) COLLATE utf8_unicode_ci NOT NULL COMMENT 'reference to downloadable document',
  `ref_view` varchar(2000) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'introduction to viewable version of document',
  `gfx_who` varchar(160) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Graphic of who',
  PRIMARY KEY (`ffid`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AVG_ROW_LENGTH=174 COMMENT='Follow-up files' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `inpt`
--

CREATE TABLE IF NOT EXISTS `inpt` (
  `tid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Thesen ID',
  `qi` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'QuestionID (new)',
  `kid` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'consultation ID',
  `dir` int(12) NOT NULL DEFAULT '0' COMMENT 'folder id for voting preparation',
  `thes` varchar(330) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'User reply',
  `expl` varchar(2000) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Longer explanation',
  `uid` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT 'from which User ID',
  `when` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Input given when',
  `block` enum('y','n','u') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'u' COMMENT 'yes, no, unchecked',
  `user_conf` enum('u','c','r') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'u' COMMENT 'unconfirmed, confirmed, rejected',
  `vot` enum('y','n','u') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'u' COMMENT 'Zum Voting zugelassen',
  `typ` enum('p','f','l','bp') COLLATE utf8_unicode_ci NOT NULL COMMENT 'Problemanzeige, Forderung, Lösungsvorschlag, Best Practice',
  `spprts` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT 'how many pressed support-haken',
  `votes` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT 'Votes received',
  `pts` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT 'Points received',
  `rel_tid` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Related tids',
  `tg_nrs` varchar(55) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Nummern der Keywords (100-999), max 14 Tags',
  `notiz` varchar(300) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Notes for internal use',
  `confirm_key` varchar(64) COLLATE utf8_unicode_ci DEFAULT '',
  PRIMARY KEY (`tid`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AVG_ROW_LENGTH=109 CHECKSUM=1 COMMENT='User input to questions' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `inpt_tgs`
--

CREATE TABLE IF NOT EXISTS `inpt_tgs` (
  `tg_nr` int(11) NOT NULL COMMENT 'id of tag',
  `tid` int(11) NOT NULL COMMENT 'id of these (input)',
  PRIMARY KEY (`tg_nr`,`tid`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AVG_ROW_LENGTH=9 ROW_FORMAT=FIXED;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `ml_def`
--

CREATE TABLE IF NOT EXISTS `ml_def` (
  `mid` smallint(5) unsigned NOT NULL AUTO_INCREMENT COMMENT 'MailID',
  `refnm` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Reference name, e.g. footer, header',
  `kid` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'consultation-specific or general (=0)',
  `proj` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'which project(s)',
  `ln` char(2) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Language of mail',
  `subj` varchar(120) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Subject of e-mail (todo check maxlength in RFC)',
  `txt` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'TEXT mail',
  `html` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'HTML mail',
  `expl` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Explanation and possible variables',
  `head` enum('y','n') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n' COMMENT 'Attach header to mail',
  `foot` enum('y','n') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n' COMMENT 'Attach footer to mail',
  PRIMARY KEY (`mid`) USING BTREE
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AVG_ROW_LENGTH=240 CHECKSUM=1 COMMENT='Mail definitions / defaults' AUTO_INCREMENT=26 ;

--
-- Daten für Tabelle `ml_def`
--

INSERT INTO `ml_def` (`mid`, `refnm`, `kid`, `proj`, `ln`, `subj`, `txt`, `html`, `expl`, `head`, `foot`) VALUES
(3, 'vot_grpmem_conf', 0, 'sd', 'de', 'Strukturierter Dialog: Jemand hat  für euch abgestimmt - bitte bestätigen!', 'Hallo {{RECIPIENT}},\r\n\r\nim Rahmen eurer Teilnahme an der Beteiligungsrunde „{{KID_TITL}}“ hat jemand neu für eure Gruppe abgestimmt. Bitte bestätige, ob „{{VOTER}}“ für euch abstimmungsberechtigt ist.\r\n\r\nJa, diese Person gehört zu unserer Gruppe:\r\n{{CONFIRMLINK}}\r\n\r\nNein, diese Person gehört nicht zu unserer Gruppe:\r\n{{REJECTLINK}}\r\n\r\n\r\nMit freundlichen Grüßen\r\n\r\nDeine Koordinierungsstelle für den Strukturierten Dialog\r\n\r\n', '', '{{RECIPIENT}} {{KID_TITL}} {{VOTER}} {{VTC}} {{SUB_UID}} {{VOTERYEA}} {{VOTERNAY}}', 'n', 'y'),
(21, 'inpt_conf', 0, 'xx', 'de', 'Beteiligungsrunde „{{CNSLT_TITLE_SHORT}}“: Bitte Einträge bestätigen', 'Hallo {{USER}},\r\n\r\ndanke für die Beteiligung an „{{CNSLT_TITLE}}“ im Rahmen von XXX. Unten findest Du eine Übersicht deiner/eurer Antworten auf die Fragestellungen.\r\n\r\nUm sicherzustellen, dass die Einträge nicht von einem Spamversender kommen, bitten wir dich um die Bestätigung der Eingaben über den folgenden Link:\r\n\r\n{{SITEURL}}{{CONFIRMLINK}}{{CID}}\r\n\r\nFalls sich der Bestätigungslink nicht anklicken lässt oder ein Teil des Links abgeschnitten wurde, kopiere bitte den gesamten Link und füge ihn in die Adressleiste Deines Browsers ein. Drücke anschließend die Eingabetaste.\r\n\r\nWenn die Beiträge nicht von dir sind, kannst du sie mithilfe dieses Links ablehnen:\r\n\r\n{{REJECTLINK}}\r\n\r\nDer Bestätigungslink verliert nach dem Ende der Beteiligungsphase seine Gültigkeit. Deine Zugangsdaten hast du bereits erhalten. Solltest du sie vergessen haben, kannst du mit Eingabe deiner E-Mail-Adresse jederzeit ein neues Passwort anfordern. Deine Beiträge kannst du im Login-Bereich bis zum Ende der ersten Beteiligungsphase weiter bearbeiten, ergänzen oder löschen.\r\n\r\nDas Sammeln von Beiträgen und damit die erste Beteiligungsphase endet am {{INPUT_TO}}. Danach werden wir alle Teilnehmenden bitten, aus den gesammelten Beiträgen diejenigen auszuwählen, die ihnen am wichtigsten sind. Dazu erhältst du/erhaltet ihr eine E-Mail mit weiteren Informationen von uns.\r\n\r\n\r\nBei Rückfragen stehen dir NAME und NAME im Projektbüro von XXX gern zur Verfügung. Einfach anmailen (XXX@XXX.de) oder anrufen unter 000/0000000.\r\n\r\nLiebe Grüße,\r\nEuer Projektbüro von XXX\r\n\r\n==============================================================================\r\n\r\nÜbersicht über Eure Beiträge zur Beteiligungsrunde\r\n„{{CNSLT_TITLE}}“\r\n\r\n==============================================================================\r\n\r\n{{USER_INPUTS}}\r\n\r\n', '', '{{USER}} {{CNSLT_TITLE}} {{CNSLT_TITLE_SHORT}} {{INPUT_TO}} {{CONFIRMLINK}} {{REJECTLINK}} {{USER_INPUTS}}', 'n', 'y'),
(22, 'register', 0, 'xx', 'de', 'Registrierung für "PROJEKTNAME"', 'Hallo {{USER}},\r\n\r\ndanke für deine/eure Beteiligung an der aktuellen Beteiligungsrunde im Rahmen von XXX. Es wurde ein Passwort generiert, mit dem du/ihr dich/euch in Zukunft am System anmelden kannst/könnt:\r\n\r\n{{PASSWORD}}\r\n\r\nMit dieser Mail bitten wir dich/euch um die Bestätigung deiner/eurer Registrierung. Klickt dazu bitte auf folgenden Link oder kopiert diesen in die Adresszeile deines/eures Browsers:\r\n\r\n{{CONFIRMLINK}}\r\n\r\nNach der Bestätigung erhältst du/ihr eine Mail zur Bestätigung deiner/eurer Beiträge.\r\n\r\nLiebe Grüße,\r\neuer Projektteam von PROJEKTNAME\r\n\r\n', '', '{{USER}} {{PASSWORD}} {{CONFIRMLINK}}', 'n', 'y'),
(6, 'vt_invit_single', 0, 'sd', 'de', 'Konsultation „{{CNSLT_TITLE}}“: Jetzt abstimmen!', 'Hallo {{USER}},\r\n\r\ndu hast an der Beteiligungsrunde zu „{{CNSLT_TITLE}}“ teilgenommen. Noch einmal herzlichen Dank für deine Beiträge!\r\n\r\nIn der zweiten Phase der Beteiligungsrunde geht es nun darum, die gesammelten Beiträge zu bewerten. Deshalb haben du und alle anderen Teilnehmenden vom {{VOTE_FROM}} bis {{VOTE_TO}} die Möglichkeit, online darüber abzustimmen, welche der Beiträge besonders wichtig für die weitere politische Diskussion zum Thema sind. Auf der Grundlage eurer Bewertungen werden wir am Ende die Zusammenfassung erstellen.\r\n\r\nDie Abstimmung erfolgt anonym. Die IP-Adresse deines Computers wird nicht gespeichert.\r\n\r\nHier geht’s los:\r\n{{VOTINGURL}}\r\n\r\nSollten technische Probleme auftreten oder Fragen aufkommen, stehen dir Ann-Kathrin Fischer und Tim Schrock gerne zur Verfügung. Einfach anmailen (sd@dbjr.de) oder anrufen unter 030. 400 40 443.\r\n\r\nMit freundlichen Grüßen\r\n\r\nDeine Koordinierungsstelle für den Strukturierten Dialog\r\n\r\n', '', '{{USER}} {{CNSLT_TITLE}} {{VOTE_FROM}} {{VOTE_TO}} {{SITEURL}} {{VTC}}', 'n', 'y'),
(7, 'vt_invit_group', 0, 'sd', 'de', 'Konsultation „{{CNSLT_TITLE}}“: Jetzt abstimmen!', 'Hallo {{USER}},\r\n\r\nihr habt euch an der Beteiligungsrunde zu „{{CNSLT_TITLE}}“ als Gruppe beteiligt. Noch einmal herzlichen Dank für eure Beiträge! \r\n\r\nIn der Abstimmungsphase der Beteiligungsrunde geht es nun darum, die gesammelten Beiträge zu bewerten. Deshalb habt ihr und alle anderen Teilnehmenden vom {{VOTE_FROM}} bis {{VOTE_TO}} die Möglichkeit, online darüber abzustimmen, welche der Beiträge besonders wichtig für die weitere politische Diskussion zum Thema sind. Auf der Grundlage aller Bewertungen werden wir am Ende die Zusammenfassung erstellen. \r\n\r\nDu wurdest als Kontaktperson für diese Gruppe eingetragen.\r\n\r\nWas ist nun deine Aufgabe?\r\n\r\n1. Eure Gruppe zählt wegen ihrer Größe zur Kategorie {{GROUP_CATEGORY}} Teilnehmer_innen und hat damit bei dieser Beteiligungsrunde ein Gewicht von {{VOTING_WEIGHT}}. Das bedeutet, egal wie viele Leute für eure Gruppe teilnehmen, in der Summe werden ihre Stimmen anteilig auf dieses Gewicht hoch- bzw. heruntergerechnet. Das heißt, ihr könnt frei entscheiden, wie viele Personen für eure Gruppe an der 2. Phase teilnehmen sollen: eine, zwölf, dreiundfünfzig, hundert oder mehr!\r\n\r\n2. Leite denjenigen, die für eure Gruppe an der Abstimmung teilnehmen sollen, den unten stehenden Zugangslink weiter. Nach Eingabe einer E-Mail-Adresse können diese Personen mit der Abstimmung beginnen. Diese erfolgt anonym.\r\n\r\n3. Damit du als Kontaktpersonen den Überblick behältst, wer sich für eure Gruppe beteiligt, und um Missbrauch zu vermeiden, musst du anschließend bestätigen, dass die Personen, die teilgenommen haben, auch dazu berechtigt waren. Dies geschieht ganz einfach per E-Mail.\r\n\r\n4. Am Ende des Abstimmungszeitraums werdet ihr von uns selbstverständlich über das Endergebnis informiert.\r\n\r\n\r\nDer Zugangslink für Ihre/eure Gruppe lautet:\r\n{{VOTINGURL}}\r\n\r\n***\r\nVorschlag für ein Anschreiben an die Mitglieder deiner Gruppe:\r\n\r\nWir haben an der Beteiligungsrunde „{{CNSLT_TITLE}}“ teilgenommen. Bis {{VOTE_TO}} haben nun alle Teilnehmer_innen die Möglichkeit, online darüber abzustimmen, welche der Beiträge aus ihrer Sicht besonders wichtig für die weitere politische Diskussion zum Thema sind. Auf der Grundlage des Abstimmungsergebnisses wird am Ende die Zusammenfassung erstellt.\r\n\r\nMacht mit und stimmt mit ab. Hier geht’s los:\r\n{{VOTINGURL}}\r\n\r\n***\r\n\r\nSollten technische Probleme auftreten oder Fragen aufkommen, stehen euch Ann-Kathrin Fischer und Tim Schrock gerne zur Verfügung. Einfach anmailen (sd@dbjr.de) oder anrufen unter 030. 400 40 443.\r\n\r\n\r\nMit freundlichen Grüßen\r\n\r\nEure Koordinierungsstelle für den Strukturierten Dialog\r\n\r\n', '', '{{USER}} {{CNSLT_TITLE}} {{VOTE_FROM}} {{VOTE_TO}} {{SITEURL}} {{VTC}} {{GROUP_CATEGORY}} {{VOTING_WEIGHT}}', 'n', 'y'),
(23, 'pwdalter', 0, 'xx', 'de', 'Neue Zugangsdaten für „PROJEKTNAME“', 'Hallo {{USER}},\r\n\r\n   eure Zugangsdaten wurden geändert.\r\n   Mit den folgenden Daten könnt ihr euch einloggen:\r\n\r\n      Username: {{EMAIL}}\r\n      Passwort: {{PWD}}\r\n\r\n   Bei Fragen:', '<p>Hallo {{USER}},</p>\r\n\r\n<p>&nbsp;&nbsp; eure Zugangsdaten wurden ge&auml;ndert.<br />\r\n&nbsp;&nbsp; Mit den folgenden Daten k&ouml;nnt ihr euch einloggen:</p>\r\n\r\n<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Username: {{EMAIL}}<br />\r\n&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Passwort: {{PWD}}</p>\r\n\r\n<p>&nbsp;&nbsp; Bei Fragen:</p>\r\n', '{{USER}} {{EMAIL}} {{PWD}}', 'n', 'y'),
(10, 'vt_invit_group', 0, 'xx', 'de', 'Beteiligungsrunde „{{CNSLT_TITLE}}“: Jetzt abstimmen!', 'Hallo {{USER}},\r\n\r\nIhr habt euch an der Beteiligungsrunde „{{CNSLT_TITLE}}“ als Gruppe beteiligt. Du wurdest als Kontaktperson für diese Gruppe eingetragen.\r\n\r\nIm zweiten Schritt geht es nun  darum, die gesammelten Beiträge zusammenzufassen. Deshalb haben alle Teilnehmer_innen vom {{VOTE_FROM}} bis {{VOTE_TO}} die Möglichkeit darüber abzustimmen, welche der Meinungen, Positionen und Vorschläge ihnen am wichtigsten sind. Auf der Grundlage des Abstimmungsergebnisses wird am Ende die Zusammenfassung erstellt.\r\n\r\nWie funktioniert das?\r\n\r\n1. Ihr könnt euch frei entscheiden, wie viele Personen für eure Gruppe an der Abstimmung teilnehmen sollen: eine, zwölf oder hundert (oder mehr!). Eure Gruppe zählt zur Kategorie {{GROUP_CATEGORY}} Teilnehmer_innen und hat damit bei dieser Konsultation ein Gewicht von {{VOTING_WEIGHT}}. Das bedeutet, egal wie viele Leute für Eure Gruppe abstimmen, in der Summe werden ihre Stimmen anteilig auf dieses Gewicht hoch- bzw. heruntergerechnet.\r\n\r\n2. Leitet denjenigen, die für die Gruppe an der Abstimmung teilnehmen sollen, den unten stehenden Zugangslink weiter. Nach Eingabe einer E-Mail-Adresse können diese Personen mit der Abstimmung beginnen. Diese erfolgt anonym.\r\n\r\n3. Damit ihr als Kontaktpersonen den Überblick behaltet, wer für eure Gruppe abstimmt, und um Missbrauch zu vermeiden, müsst ihr anschließend bestätigen, dass die Personen, die abgestimmt haben, auch dazu berechtigt waren. Dies geschieht ganz einfach per E-Mail.\r\n\r\n4. Am Ende des Abstimmungszeitraums werdet ihr automatisch über das Endergebnis informiert.\r\n\r\n\r\nDer Zugangslink für eure Gruppe:\r\n{{SITEURL}}{{VOTINGURL}}{{CID}}\r\n\r\n***\r\nVorschlag für ein Anschreiben an die Mitglieder Eurer Gruppe:\r\n\r\nWir haben uns an der Beteiligungsrunde „{{CNSLT_TITLE}}“ teilgenommen. Nun haben alle Teilnehmer_innen vom {{VOTE_FROM}} bis {{VOTE_TO}} die Möglichkeit darüber abzustimmen, welche der gesammelten Meinungen, Vorschläge und Positionen ihnen am wichtigsten sind. Auf der Grundlage des Abstimmungsergebnisses wird am Ende die Zusammenfassung erstellt.\r\n\r\nMacht mit und stimmt mit ab. Hier geht’s los:\r\n{{SITEURL}}{{VOTINGURL}}{{CID}}\r\n\r\n***\r\n\r\nBei Fragen steht dir das Projektteam von PROJEKTNAME gerne zur Verfügung.\r\nEinfach anmailen (XXX@XXX.de) oder anrufen unter 000/0000000.\r\n\r\nLiebe Grüße,\r\neuer Projektbüro von PROJEKTNAME.', '', '{{USER}} {{CNSLT_TITLE}} {{VOTE_FROM}} {{VOTE_TO}} {{SITEURL}} {{VTC}} {{GROUP_CATEGORY}} {{VOTING_WEIGHT}}', 'n', 'y'),
(11, 'vt_invit_single', 0, 'xx', 'de', 'Beteiligungsrunde „{{CNSLT_TITLE}}“: Jetzt abstimmen!', 'Hallo {{USER}},\r\n\r\nDu hast bei der Beteiligungsrunde zu „{{CNSLT_TITLE}}“ mitgemacht. \r\nInsgesamt haben wir eine große Anzahl an Rückmeldungen erhalten. Nun geht es darum, die Beiträge zusammenzufassen. \r\nDeshalb können alle Teilnehmer_innen vom {{VOTE_FROM}} bis {{VOTE_TO}} darüber abstimmen, welche der gesammelten Meinungen, Positionen und Vorschläge ihnen am wichtigsten sind. \r\nAuf der Grundlage des Abstimmungsergebnisses wird am Ende die Zusammenfassung erstellt.\r\n\r\nHier geht’s los:\r\n{{SITEURL}}{{VOTINGURL}}{{CID}\r\n\r\nLiebe Grüße,\r\ndein Projektbüro von PROJEKTNAME.\r\n', '', '{{USER}} {{CNSLT_TITLE}} {{VOTE_FROM}} {{VOTE_TO}} {{SITEURL}} {{VTC}}', 'n', 'y'),
(1, 'footer', 0, 'xx', 'de', '(ohne Betreff, da Fußzeile)', '-\r\nProjektname\r\nAnsprechperson\r\nTelefonnummer\r\nAdresse\r\n\r\nWebseite\r\nSocial Media Angebot\r\n\r\netc.\r\n', '', '', 'n', 'n'),
(13, 'pwdrequest', 0, 'xx', 'de', 'Neue Zugangsdaten für „PROJEKTNAME“', 'Hallo {{USER}},\r\n\r\n   eure Zugangsdaten wurden geändert.\r\n   Mit den folgenden Daten könnt ihr euch einloggen:\r\n\r\n      Username: {{EMAIL}}\r\n      Passwort: {{PWD}}\r\n\r\n   Bei Fragen:', '<p>Hallo {{USER}},</p>\r\n\r\n<p>&nbsp;&nbsp; eure Zugangsdaten wurden ge&auml;ndert.<br />\r\n&nbsp;&nbsp; Mit den folgenden Daten k&ouml;nnt ihr euch einloggen:</p>\r\n\r\n<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Username: {{EMAIL}}<br />\r\n&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Passwort: {{PWD}}</p>\r\n\r\n<p>&nbsp;&nbsp; Bei Fragen:</p>\r\n', '{{USER}} {{EMAIL}} {{PWD}}', 'n', 'y'),
(19, 'register', 0, 'sd', 'de', 'Registrierung für den Strukturierten Dialog', 'Hallo {{USER}},\r\n\r\ndanke für deine/eure Beteiligung an der aktuellen Beteiligungsrunde im Rahmen des Strukturierten Dialogs. Es wurde ein Passwort generiert, mit dem du dich/ihr euch in Zukunft im System anmelden kannst/könnt:\r\n\r\n{{PASSWORD}}\r\n\r\nMit dieser Mail bitten wir dich/euch um die Bestätigung deiner/eurer Registrierung. Klickt dazu bitte auf den folgenden Link oder kopiert diesen in die Adresszeile deines/eures Browsers:\r\n\r\n{{CONFIRMLINK}}\r\n\r\nNach der Bestätigung erhältst du/erhaltet ihr eine Mail zur Bestätigung deiner/eurer Beiträge.\r\n\r\nMit freundlichen Grüßen\r\ndeine/eure Koordinierungsstelle für den Strukturierten Dialog\r\n\r\n', '<p>Hallo {{USER}}</p>\r\n', '{{USER}} {{PASSWORD}} {{CONFIRMLINK}}', 'n', 'y'),
(24, 'vot_conf', 0, 'xx', 'de', 'Vielen Dank fürs Abstimmen! Bitte bestätige deine Teilnahme!', 'Hallo {{USER}},\r\n\r\nvielen Dank für deine Teilnahme an der Abstimmung zur Beteiligungsrunde „{{KID_TITL}}“.  \r\n\r\nDamit wir sicherstellen können, dass wirklich du selbst abgestimmt hast, bitten wir dich, deine Teilnahme über den unten stehenden Link zu bestätigen.\r\n\r\nJa, ich habe abgestimmt: \r\n{{URLCONFIRM}}\r\n\r\nSolltest du nicht an der Abstimmung teilgenommen haben, klicke auf diesen Link: \r\n{{URLREJCT}}\r\n\r\nFalls sich der Bestätigungslink nicht anklicken lässt oder ein Teil des Links abgeschnitten wurde, kopiere bitte den gesamten Link und füge ihn in die Adressleiste deines Browsers ein. Drücke anschließend die Eingabetaste.\r\n\r\nWir freuen uns über dein Interesse und werden dich nach Abschluss der Beteiligungsrunde per E-Mail über die Ergebnisse informieren.\r\n\r\nLiebe Grüße,\r\ndein Projektbüro von PROJEKTNAME.\r\n', '', '{{USER}} {{KID_TITL}} {{SITEURL}} {{VTC}} {{SUB_UID}}', 'n', 'y'),
(25, 'vot_grpmem_conf', 0, 'xx', 'de', 'PROJEKTNAME: Jemand hat  für euch abgestimmt - bitte bestätigen!', 'Hallo {{RECIPIENT}},\r\n\r\nim Rahmen eurer Teilnahme an der Beteiligungsrunde „{{KID_TITL}}“ hat jemand neu für eure Gruppe abgestimmt. Bitte bestätige, ob „{{VOTER}}“ für euch abstimmungsberechtigt ist.\r\n\r\nJa, diese Person gehört zu unserer Gruppe:\r\n{{CONFIRMLINK}}\r\n\r\nNein, diese Person gehört nicht zu unserer Gruppe:\r\n{{REJECTLINK}}\r\n\r\n\r\nLiebe Grüße,\r\neuer Projektbüro von PROJEKTNAME.\r\n', '', '{{RECIPIENT}} {{KID_TITL}} {{VOTER}} {{VTC}} {{SUB_UID}} {{VOTERYEA}} {{VOTERNAY}}', 'n', 'y');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `ml_sent`
--

CREATE TABLE IF NOT EXISTS `ml_sent` (
  `when` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `rec` varchar(60) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Recipient of mail',
  `sender` varchar(60) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'sent by',
  `subj` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'subject of mail sent',
  `proj` varchar(8) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Sent from which project',
  `ip` varchar(15) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '' COMMENT 'IP address of sender',
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `message` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AVG_ROW_LENGTH=70 COMMENT='Mails sent by system' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `proj`
--

CREATE TABLE IF NOT EXISTS `proj` (
  `proj` char(2) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Project abbrev',
  `titl_short` varchar(80) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Short title/name for project',
  `email` varchar(80) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Address which the tool uses for sending out messages (todo check maxlength by rfc)',
  `realnm` varchar(80) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Real name to be uses as sender alias for e-mail',
  `smtp_srv` varchar(60) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'SMTP server (not used yet)',
  `smtp_prt` tinyint(3) unsigned NOT NULL DEFAULT '25' COMMENT 'SMTP port (not used yet)',
  `smtp_usr` varchar(40) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'SMTP user (not used yet)',
  `smtp_pwd` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'SMTP password (not used yet)',
  `toolline` varchar(260) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Tool headline itself',
  `vot_q` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Question used for voting',
  PRIMARY KEY (`proj`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AVG_ROW_LENGTH=20 COMMENT='All projects active in this installation';

--
-- Daten für Tabelle `proj`
--

INSERT INTO `proj` (`proj`, `titl_short`, `email`, `realnm`, `smtp_srv`, `smtp_prt`, `smtp_usr`, `smtp_pwd`, `toolline`, `vot_q`) VALUES
('xx', 'ePartool (default)', 'epartool@dbjr.de', 'ePartool Grundinstallation', '', 0, '', '', '', 'Wie wichtig findest Du diesen Beitrag für die weitere politische Diskussion zum Thema?');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `quests`
--

CREATE TABLE IF NOT EXISTS `quests` (
  `qi` int(10) unsigned NOT NULL COMMENT 'QuestionID (new)',
  `kid` smallint(5) unsigned NOT NULL COMMENT 'Consultation ID',
  `nr` char(4) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Number shown in ordered list',
  `q` varchar(300) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'The question itself',
  `q_xpl` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'Explanation for question',
  `ln` char(2) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '' COMMENT 'Language',
  `vot_q` varchar(220) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Introducing voting question',
  UNIQUE KEY `qi` (`qi`) USING BTREE,
  KEY `qi_2` (`qi`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AVG_ROW_LENGTH=93 CHECKSUM=1 COMMENT='Questions for the consultations';

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `quests_choic`
--

CREATE TABLE IF NOT EXISTS `quests_choic` (
  `qi` int(10) unsigned NOT NULL COMMENT 'ref to QuestionID',
  `opt` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Option for reference',
  `desc` varchar(240) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Descriptive label',
  `ln` char(2) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'de' COMMENT 'Language'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Options for multiple choice questions';

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `sessns`
--

CREATE TABLE IF NOT EXISTS `sessns` (
  `uid` mediumint(8) unsigned NOT NULL COMMENT 'User ID',
  `cid` char(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '' COMMENT 'Confirmation code',
  `sess_strt` datetime NOT NULL COMMENT 'When session started',
  `sess_end` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'When last activity took place',
  `ip` char(15) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '' COMMENT 'IP Address User',
  `agt` varchar(120) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '' COMMENT 'User agent (Browser)',
  `name_pers` varchar(80) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Name of contact person',
  `name_grp` varchar(80) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Name of group',
  `source` set('d','g','m','p') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'm' COMMENT 'Dialogue, Group, Misc, Position paper',
  `src_misc` varchar(300) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'explanation of misc source',
  `grp_size` tinyint(3) unsigned NOT NULL COMMENT '1,10,30,80,150,over',
  `regio_pax` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Bundesländer',
  `age_grp` enum('1','2','3','4','5') COLLATE utf8_unicode_ci NOT NULL DEFAULT '5' COMMENT '(1)to17yrs(2)upto26yrs(3)upto27yrs(4)all(5)noinfo',
  `find_out` varchar(120) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'woher kennt ihr uns',
  `group_type` varchar(60) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Art der Gruppe',
  `what_you_do` enum('school','education','work','selfemployed','volunteer','unemployed','notspecified') COLLATE utf8_unicode_ci NOT NULL,
  `kid` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'KID, falls Eintragung abgeschlossen',
  `publ_us` enum('n','y') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n' COMMENT 'publicize us a contributor',
  `cnslt_results` enum('y','n') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'y' COMMENT 'Receives results of consultations',
  `cmnt_ext` varchar(600) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Comment by user',
  PRIMARY KEY (`uid`,`sess_strt`,`kid`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AVG_ROW_LENGTH=184 CHECKSUM=1 COMMENT='Users in the system';

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `supports`
--

CREATE TABLE IF NOT EXISTS `supports` (
  `tid` int(10) unsigned NOT NULL COMMENT 'ref to TID',
  `uid` mediumint(8) unsigned NOT NULL COMMENT 'uid if existing',
  `tmphash` char(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'hash from IP etc',
  PRIMARY KEY (`tid`,`uid`,`tmphash`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=FIXED;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tgs`
--

CREATE TABLE IF NOT EXISTS `tgs` (
  `tg_nr` smallint(5) unsigned NOT NULL AUTO_INCREMENT COMMENT 'tag number',
  `tg_de` varchar(40) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'German translation of tag',
  `tg_en` varchar(40) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'English translation of tag',
  PRIMARY KEY (`tg_nr`) USING BTREE
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AVG_ROW_LENGTH=26 COMMENT='Schlagwörter intern' AUTO_INCREMENT=490 ;

--
-- Daten für Tabelle `tgs`
--

INSERT INTO `tgs` (`tg_nr`, `tg_de`, `tg_en`) VALUES
(101, 'Bund', ''),
(102, 'Länder', ''),
(103, 'europäische Ebene', ''),
(104, 'regional', ''),
(106, 'Jugendverbände', ''),
(108, 'altersgemäß', ''),
(110, 'Aktivitäten', ''),
(111, 'aktiv werden', ''),
(112, 'auf allen Ebenen', ''),
(113, 'Beteiligungsangebote schaffen', ''),
(114, 'Bildung', ''),
(116, 'direkter Bezug', ''),
(117, 'contra Wahlaltersenkung', ''),
(118, 'Dialog', ''),
(119, 'Distanz', ''),
(120, 'Einfluss nehmen können', ''),
(121, 'Jugendliche ernst nehmen', ''),
(122, 'Europa', ''),
(123, 'Familie', ''),
(124, 'greifbar', ''),
(125, 'Glaubwürdigkeit', ''),
(126, 'Image', ''),
(127, 'Information', ''),
(128, 'Integration', ''),
(129, 'jugendfreundliche Ansprache', ''),
(130, 'Jugendinteressen berücksichtigen', ''),
(131, 'Wahlkampagnen', ''),
(132, 'Kompetenz', ''),
(133, 'Kommune', ''),
(134, 'Bundesland', ''),
(135, 'Medien', ''),
(136, 'Methoden', ''),
(137, 'Meinung bilden', ''),
(138, 'Parteien', ''),
(139, 'passives Wahlrecht', ''),
(140, 'politische Bildung', ''),
(141, 'Politiker_innen', ''),
(142, 'Wahlprogramme', ''),
(143, 'Kontakt, persönlicher', ''),
(144, 'pro Wahlaltersenkung', ''),
(145, 'Rahmenbedingungen', ''),
(147, 'Transparenz', ''),
(149, 'Vorbilder', ''),
(150, 'Vorbereitung', ''),
(151, 'Wählen unter 14', ''),
(152, 'Wählen ab 14', ''),
(153, 'Wählen ab 16', ''),
(154, 'Wählen ab 18', ''),
(155, 'Wahlakt', ''),
(156, 'Wählen ist nicht alles', ''),
(157, 'Internet/soziale Netzwerke', ''),
(158, 'Zugang', ''),
(159, 'Ziele', ''),
(160, 'Klarheit', ''),
(162, 'Jugendbeteiligung verankern', ''),
(163, 'Demokratie', ''),
(164, 'Demokratie, direkte', ''),
(165, 'Engagement', ''),
(166, 'ePartizipation', ''),
(167, 'Gesetz', ''),
(168, 'Jugendinitiativen', ''),
(169, 'Motivation', ''),
(170, 'neue Beteiligungsformen', ''),
(171, 'Praxisbeispiel', ''),
(172, 'Reife', ''),
(173, 'UN-Kinderrechte', ''),
(174, 'Verbände', ''),
(175, 'Wählen ab 15', ''),
(146, 'Schule', ''),
(223, 'Abiturient_innen', ''),
(224, 'Anerkennung', ''),
(225, 'Auszubildende', ''),
(226, 'benachteiligte Jugendliche', ''),
(227, 'Beratung', ''),
(228, 'Bewerbung', ''),
(229, 'Chancen/Potenziale', ''),
(230, 'Erfahrung', ''),
(231, 'Förderung', ''),
(232, 'Freiwilligendienst', ''),
(233, 'Freiwilligenprogramme', ''),
(234, 'ehrenamtliches Engagement', ''),
(235, 'Hauptschüler_innen', ''),
(236, 'Hindernisse', ''),
(237, 'Interesse', ''),
(238, 'Intransparenz', ''),
(239, 'junge Berufstätige', ''),
(240, 'Kosten', ''),
(241, 'Realschüler_innen', ''),
(242, 'Sprache', ''),
(243, 'Studium', ''),
(244, 'Unsicherheit', ''),
(245, 'Unübersichtlichkeit', ''),
(246, 'Vielfalt an Möglichkeiten', ''),
(247, 'wenig bekannt', ''),
(257, 'Zertifikat', ''),
(258, 'Selbstvertrauen', ''),
(259, 'Teamfähigkeit', ''),
(260, 'Organisationsfähigkeit', ''),
(261, 'Partizipation', ''),
(262, 'Kommunikation', ''),
(263, 'kritisches Denken', ''),
(264, 'Toleranz', ''),
(265, 'Verantwortung', ''),
(266, 'Wertschätzung', ''),
(267, 'Konflikte lösen können', ''),
(268, 'individuelle Fähigkeiten', ''),
(269, 'demokratische Strukturen', ''),
(270, 'Einfühlungsvermögen', ''),
(271, 'Selbstständigkeit', ''),
(272, 'Gleichberechtigung', ''),
(273, 'eigene Projekte umsetzen', ''),
(274, 'soziales Engagement', ''),
(276, 'Alltagskompetenz', ''),
(277, 'Zukunft, berufliche', ''),
(278, 'soziale Kompetenz', ''),
(279, 'Aushandlungsprozesse', ''),
(280, 'Kreativität', ''),
(281, 'Bildung, außerschulische', ''),
(282, 'sich ausprobieren', ''),
(283, 'Lösungsansätze finden', ''),
(284, 'Meinung vertreten', ''),
(285, 'dazu gehören', ''),
(286, 'Rechte', ''),
(287, 'Gemeinschaft', ''),
(289, 'Problemmanagement', ''),
(290, 'fachspezifische Kenntnisse', ''),
(292, 'Leitungsfunktion', ''),
(293, 'Pädagogik', ''),
(294, 'sich einsetzen', ''),
(295, 'Nachhaltigkeit', ''),
(296, 'Offenheit', ''),
(297, 'Flexibilität', ''),
(298, 'Gesellschaft', ''),
(299, 'Selbstreflexion', ''),
(300, 'Unterstützung, finanzielle', ''),
(301, 'Sonderurlaub', ''),
(302, 'ideelle Unterstützung', ''),
(303, 'Leistung', ''),
(304, 'Unternehmen', ''),
(305, 'geringe Wertschätzung', ''),
(306, 'Investition', ''),
(307, 'Auszeichnung', ''),
(308, 'hohe Wertschätzung', ''),
(309, 'Unterstützung', ''),
(310, 'Einstellungskriterium', ''),
(311, 'erschwerte Durchführung', ''),
(312, 'Praxis', ''),
(313, 'Akzeptanz', ''),
(314, 'Dankbarkeit', ''),
(315, 'Freizeit', ''),
(316, 'Bewertung, quantitative', ''),
(317, 'Bewertung, qualitative', ''),
(319, 'Bildungsurlaub', ''),
(320, 'Aufwand', ''),
(321, 'persönlich/individuell', ''),
(322, 'wissenschaftliche Studien', ''),
(323, 'Öffentlichkeit(sarbeit)', ''),
(324, 'Personal', ''),
(325, 'Dokumentation/Nachweis', ''),
(326, 'Standard(s)', ''),
(327, 'einheitlich', ''),
(328, 'Aktion/Kampagne', ''),
(329, 'Vergünstigungen', ''),
(330, 'Vernetzung', ''),
(331, 'Vorteile', ''),
(332, 'Ausstattung', ''),
(333, 'Mitbestimmung', ''),
(334, 'weniger Bürokratie', ''),
(335, 'freier Nachmittag', ''),
(336, 'Leiterausbildung', ''),
(337, 'Vorurteile', ''),
(338, 'Lobby', ''),
(339, 'Freiräume', ''),
(340, 'Prozess', ''),
(341, 'Freiwilligkeit', ''),
(342, 'Lernen', ''),
(343, 'unterschiedliche Relevanz', ''),
(344, 'Spaß', ''),
(345, 'Politik', ''),
(346, 'Zeit(management)', ''),
(347, 'Freunde', ''),
(348, 'Grenzen erfahren', ''),
(349, 'Zuverlässigkeit', ''),
(350, 'Natur', ''),
(351, 'Fairness', ''),
(352, 'steigende Anerkennung', ''),
(353, 'Gleichstellung', ''),
(354, 'Arbeitgeber_innen', ''),
(355, 'junge Menschen', ''),
(356, 'erwünscht/vorausgesetzt', ''),
(357, 'Wertschätzung, mittlere', ''),
(358, 'keine Wertschätzung', ''),
(359, 'fehlendes Bewusstsein', ''),
(360, 'Anerkennung, langsame', ''),
(361, 'unterschätzt', ''),
(362, 'Alter', ''),
(363, 'visuelle  Ergebnisse', ''),
(364, 'Bonus', ''),
(365, 'Sichtbarkeit', ''),
(366, 'Jugendpolitik', ''),
(367, 'bundesweit', ''),
(368, 'Fort-/Weiterbildung', ''),
(369, 'gemeinsame Konzepte/Transfer', ''),
(370, 'Gruppe', ''),
(371, 'formale Bildung', ''),
(372, 'Zeugnis', ''),
(373, 'Ausbildung', ''),
(374, 'Freistellung', ''),
(375, 'Jugendarbeit/Jugendhilfe', ''),
(376, 'Wirtschaft', ''),
(377, 'Jugendleiter_innen', ''),
(378, 'Verwaltung', ''),
(379, 'Identität', ''),
(380, 'Eltern', ''),
(381, 'kulturelle Unterschiede', ''),
(382, 'Kultur', ''),
(383, 'bewusster Umgang', ''),
(384, 'interkulturelles Bewusstsein', ''),
(385, 'Inklusion', ''),
(386, 'Diskriminierung', ''),
(387, 'Gleichbehandlung', ''),
(388, 'Arbeit/Beschäftigung', ''),
(389, 'Wahlrecht', ''),
(390, 'Aufklärung', ''),
(391, 'Projekte', ''),
(392, 'Kooperation', ''),
(393, 'Religion', ''),
(394, 'Quote', ''),
(395, 'Pflicht', ''),
(396, 'Staatsbürgerschaft', ''),
(397, 'Qualifikation', ''),
(398, 'Wohnsituation', ''),
(399, 'Wissen', ''),
(400, 'Rolle', ''),
(401, 'Gremium', ''),
(402, 'Wahlen', ''),
(403, 'MJSO', ''),
(404, 'interkulturelle Öffnung', ''),
(405, 'Begegnung', ''),
(406, 'internationaler Jugendaustausch', ''),
(407, 'Ausgrenzung', ''),
(408, 'Armut', ''),
(409, 'prekäre Lebensbedingungen', ''),
(410, 'Attraktivität', ''),
(411, 'Auseinandersetzungsprozesse', ''),
(412, 'Noten', ''),
(413, 'Medienkompetenz', ''),
(414, 'Bachelor/Master', ''),
(415, 'Verzweckung', ''),
(416, 'Auslandserfahrung', ''),
(417, 'Ängste', ''),
(418, 'Lebensnähe', ''),
(419, 'Behinderung', ''),
(420, 'Lohn/Bezahlung', ''),
(421, 'Gleichaltrige/Peers', ''),
(422, 'Au Pair', ''),
(423, 'Lehrer_innen', ''),
(424, 'Fachkräftemangel', ''),
(425, 'LGBT/Queer', ''),
(467, 'Vielfalt', ''),
(427, 'Behinderung, geistige', ''),
(428, 'Behinderung, körperliche', ''),
(429, 'Leistungsdruck/Konkurrenzdenken', ''),
(430, 'finanzielle Mittel', ''),
(431, 'Anderssein', ''),
(432, 'Gewalt', ''),
(433, 'Schüler_innenvertretung', ''),
(434, 'handlungsfähig sein', ''),
(435, 'Pubertät', ''),
(436, 'Arbeitslosigkeit', ''),
(437, 'Migrationshintergrund', ''),
(438, 'Musik', ''),
(439, 'Bildungsgrad', ''),
(440, 'Klima/Atmosphäre', ''),
(441, 'offene Jugendarbeit', ''),
(442, 'sich wohlfühlen können', ''),
(443, 'Generationenunterschiede', ''),
(444, 'Mobbing', ''),
(445, 'Erziehung', ''),
(446, 'Mobilität', ''),
(447, 'Mehrfachbenachteiligung/-diskriminierung', ''),
(448, 'Bildung, frühkindliche', ''),
(449, 'Übergänge zwischen Lebensphasen', ''),
(450, 'demografischer Wandel', ''),
(451, 'Nähe (räumliche)', ''),
(452, 'thematisieren', ''),
(453, 'Flüchtlinge', ''),
(454, 'Schulabbrecher', ''),
(455, 'nicht bekannt', ''),
(456, 'Macht', ''),
(457, 'Schulleitung', ''),
(458, 'Selbstbestimmung', ''),
(459, 'Schulsystem', ''),
(460, 'Zukunft', ''),
(461, 'Ferien', ''),
(462, 'Politikverdrossenheit', ''),
(463, 'Stimmrecht', ''),
(464, 'Schwächen', ''),
(465, 'Stärken', ''),
(466, 'Empowerment', ''),
(468, 'Selbstverständlichkeit', ''),
(469, 'Miteinander', ''),
(470, 'Tabu', ''),
(471, 'Mittlerrolle', ''),
(472, 'formale Bildung', ''),
(473, 'Mangel', ''),
(474, 'Selbstbewusstsein', ''),
(475, 'ländlicher Raum', ''),
(476, 'Jugendhilfeausschuss', ''),
(477, 'Gewerkschaft', ''),
(478, 'Selbstverpflichtung', ''),
(479, 'urbaner Raum', ''),
(480, 'Ehrlichkeit', ''),
(481, 'Vertrauen', ''),
(483, 'Zielgruppe', ''),
(484, 'Angebote', ''),
(485, 'Sicherheit', ''),
(486, 'Rente', ''),
(487, 'Praktikum', ''),
(488, 'Gewerkschaft', ''),
(489, 'Austausch', '');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tgs_used`
--

CREATE TABLE IF NOT EXISTS `tgs_used` (
  `kid` smallint(5) unsigned NOT NULL COMMENT 'rel KID',
  `tg_nr` smallint(5) unsigned NOT NULL COMMENT 'number of tag',
  `modus` enum('i','v') COLLATE utf8_unicode_ci NOT NULL COMMENT 'tg freq for inputs or voting tags',
  `freq` smallint(5) unsigned NOT NULL COMMENT 'frequency of used tag per voted tids',
  PRIMARY KEY (`kid`,`tg_nr`,`modus`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=FIXED;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `uid` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT 'User ID',
  `tmid` char(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '' COMMENT 'Temp User ID',
  `block` enum('b','u','c') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'u' COMMENT 'blocked. unknown, user-confirmed',
  `ip` char(15) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '' COMMENT 'IP Address User',
  `agt` varchar(70) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '' COMMENT 'User agent (Browser)',
  `last_act` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'feststellen der letzten aktivität',
  `name` varchar(80) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Personen-/ Gruppenname',
  `email` varchar(60) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Mail Address',
  `grp` smallint(5) unsigned NOT NULL COMMENT 'Group size or single',
  `pwd` char(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Password hash',
  `newsl_subscr` enum('y','n') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n' COMMENT 'Subscription of newsletter',
  `lg` char(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Login via mail click',
  `cmnt` varchar(400) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Internal comments for admins',
  `lvl` enum('usr','adm','edt') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'usr' COMMENT 'User, Editor or Admin',
  `confirm_key` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Key zur Bestätigung der Registrierung via Mail',
  `group_type` enum('single','group') COLLATE utf8_unicode_ci DEFAULT 'single' COMMENT 'Art der Gruppe',
  `source` set('d','g','p','m') COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Dialogue, Group, Misc, Position paper',
  `src_misc` varchar(300) COLLATE utf8_unicode_ci DEFAULT '' COMMENT 'explanation of misc source',
  `group_size` tinyint(3) unsigned DEFAULT NULL COMMENT '1,10,30,80,150,over',
  `name_group` varchar(80) COLLATE utf8_unicode_ci DEFAULT '' COMMENT 'Name of group',
  `name_pers` varchar(80) COLLATE utf8_unicode_ci DEFAULT '' COMMENT 'Name of contact person',
  `age_group` enum('1','2','3','4','5') COLLATE utf8_unicode_ci NOT NULL DEFAULT '5' COMMENT '(1)to17yrs(2)upto26yrs(3)upto27yrs(4)all(5)noinfo',
  `regio_pax` varchar(200) COLLATE utf8_unicode_ci DEFAULT '' COMMENT 'Bundesländer',
  `cnslt_results` enum('y','n') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'y' COMMENT 'Receives results of consultations',
  `is_contrib_under_cc` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`) USING BTREE
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AVG_ROW_LENGTH=169 CHECKSUM=1 COMMENT='Users in the system' AUTO_INCREMENT=25 ;

--
-- Daten für Tabelle `users`
--

INSERT INTO `users` (`uid`, `tmid`, `block`, `ip`, `agt`, `last_act`, `name`, `email`, `grp`, `pwd`, `newsl_subscr`, `lg`, `cmnt`, `lvl`, `confirm_key`, `group_type`, `source`, `src_misc`, `group_size`, `name_group`, `name_pers`, `age_group`, `regio_pax`, `cnslt_results`, `is_contrib_under_cc`) VALUES
(24, 'x', 'c', '', '', '2014-01-09 15:35:11', 'Example user', 'sd-event@dbjr.de', 1, '56b39deb441b23a604a5a28a01aa48b3', 'n', 'ccb5538dfecc8a32c9c327c618c89f74', '', 'usr', '', 'single', NULL, '', NULL, '', '', '5', '', 'y', 0),
(21, 'x', 'c', '', '', '2014-01-30 12:26:46', 'Koordinierungsstelle für den Strukturierten Dialog', 'sd@dbjr.de', 1, 'd33413bf89cdce7bb9614dfd7609aa6c', 'n', 'c858c56a46f740a6f394237f17c3e0ae', '', 'adm', '', 'single', NULL, '', NULL, '', '', '5', '', 'y', 0),
(23, 'x', 'c', '213.23.240.10', '', '2014-01-30 13:39:19', 'Beteiligungstracker', 'b.tracker@strukturierter-dialog.de', 1, 'd33413bf89cdce7bb9614dfd7609aa6c', 'n', '292a13f84ccf644ef777ace6c4a09934', '', 'adm', '', 'single', NULL, '', NULL, '', '', '5', '', 'y', 0),
(22, 'x', 'c', '', '', '2014-01-31 14:49:14', 'Projektbüro Ichmache>Politik', 'ichmache-politik@dbjr.de', 0, 'c9c59b896e5715e2a6a02c78d7fee78d', 'n', '292a13f84ccf644ef777ace6c4a09934', '', 'adm', '', 'single', NULL, '', NULL, '', '', '5', '', 'y', 0);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user_info`
--

CREATE TABLE IF NOT EXISTS `user_info` (
  `user_info_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'User Info ID',
  `uid` mediumint(8) unsigned NOT NULL COMMENT 'User ID',
  `kid` smallint(5) unsigned NOT NULL COMMENT 'Consultation ID',
  `ip` char(15) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '' COMMENT 'IP Address User',
  `agt` varchar(70) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '' COMMENT 'User agent (Browser)',
  `newsl_subscr` enum('y','n') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n' COMMENT 'Subscription of newsletter',
  `grp` smallint(5) unsigned NOT NULL COMMENT 'Group size or single',
  `lg` char(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Login via mail click',
  `cmnt` varchar(400) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Internal comments for admins',
  `group_type` enum('single','group') COLLATE utf8_unicode_ci DEFAULT 'single' COMMENT 'Art der Gruppe',
  `source` set('d','g','p','m') COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Dialogue, Group, Misc, Position paper',
  `src_misc` varchar(300) COLLATE utf8_unicode_ci DEFAULT '' COMMENT 'explanation of misc source',
  `group_size` tinyint(3) unsigned DEFAULT NULL COMMENT '1,10,30,80,150,over',
  `name_group` varchar(80) COLLATE utf8_unicode_ci DEFAULT '' COMMENT 'Name of group',
  `name_pers` varchar(80) COLLATE utf8_unicode_ci DEFAULT '' COMMENT 'Name of contact person',
  `age_group` enum('1','2','3','4','5') COLLATE utf8_unicode_ci NOT NULL DEFAULT '5' COMMENT '(1)to17yrs(2)upto26yrs(3)upto27yrs(4)all(5)noinfo',
  `regio_pax` varchar(200) COLLATE utf8_unicode_ci DEFAULT '' COMMENT 'Bundesländer',
  `cnslt_results` enum('y','n') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'y' COMMENT 'Receives results of consultations',
  `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `cmnt_ext` varchar(600) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`user_info_id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AVG_ROW_LENGTH=148 CHECKSUM=1 COMMENT='User info' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `vt_final`
--

CREATE TABLE IF NOT EXISTS `vt_final` (
  `kid` smallint(5) unsigned NOT NULL COMMENT 'rel to Consultation ID',
  `tid` int(10) unsigned NOT NULL COMMENT 'related to which ttid',
  `uid` mediumint(8) unsigned NOT NULL COMMENT 'from which User ID',
  `pts` tinyint(3) unsigned NOT NULL COMMENT 'actual vote (accumulated value)',
  PRIMARY KEY (`tid`,`uid`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci CHECKSUM=1 ROW_FORMAT=FIXED COMMENT='All votes cast';

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `vt_grps`
--

CREATE TABLE IF NOT EXISTS `vt_grps` (
  `uid` mediumint(8) unsigned NOT NULL COMMENT 'rel UID',
  `sub_user` varchar(60) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'email address',
  `sub_uid` char(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'md5 of mail.kid',
  `kid` smallint(5) unsigned NOT NULL COMMENT 'rel KID',
  `member` enum('y','n','u') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'u' COMMENT 'y= confirmed by group',
  `vt_inp_list` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'list of votable tids',
  `vt_rel_qid` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'list of rel QIDs',
  `vt_tg_list` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'list of all (still) available tags for this user',
  PRIMARY KEY (`uid`,`sub_uid`,`kid`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AVG_ROW_LENGTH=65 CHECKSUM=1 COMMENT='Abstimmende werden Gruppen zugeordnet';

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `vt_indiv`
--

CREATE TABLE IF NOT EXISTS `vt_indiv` (
  `uid` mediumint(8) unsigned NOT NULL COMMENT 'ref UID',
  `sub_uid` char(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'individual subuser',
  `tid` int(10) unsigned NOT NULL COMMENT 'voted on which TID',
  `pts` tinyint(4) NOT NULL COMMENT 'the vote itself (points)',
  `pimp` enum('y','n') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `status` enum('v','s','c') COLLATE utf8_unicode_ci NOT NULL COMMENT 'v=voted, s=skipped(vorerst), c=confirmed von sub_uid',
  `upd` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'when vote was cast',
  UNIQUE KEY `sub_uid` (`sub_uid`,`tid`) USING BTREE,
  UNIQUE KEY `Stimmenzählung` (`uid`,`tid`,`sub_uid`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AVG_ROW_LENGTH=111 CHECKSUM=1 ROW_FORMAT=FIXED COMMENT='Einzelstimmen';

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `vt_rights`
--

CREATE TABLE IF NOT EXISTS `vt_rights` (
  `kid` int(5) unsigned NOT NULL COMMENT 'rel KID',
  `uid` mediumint(8) unsigned NOT NULL COMMENT 'rel UID',
  `vt_weight` smallint(5) unsigned NOT NULL COMMENT 'Voting weight of this group',
  `vt_code` char(8) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Voting access code for this group',
  `grp_siz` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT 'Group size that we recognise',
  PRIMARY KEY (`kid`,`uid`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AVG_ROW_LENGTH=36 CHECKSUM=1 ROW_FORMAT=FIXED COMMENT='Voting rights at certain consultation';

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `vt_settings`
--

CREATE TABLE IF NOT EXISTS `vt_settings` (
  `kid` int(11) NOT NULL COMMENT 'ref to KonsultationID',
  `btn_important` enum('y','n') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n' COMMENT 'Super preference button on/off',
  `btn_important_label` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Ist mir besonders wichtig' COMMENT 'Label for the super preference button',
  `btn_important_max` tinyint(4) NOT NULL DEFAULT '6' COMMENT 'Max amount of items for the super preference button',
  `btn_important_factor` tinyint(4) NOT NULL DEFAULT '3' COMMENT 'Multiplier for votes in super button',
  `btn_numbers` enum('0','1','2','3','4') COLLATE utf8_unicode_ci NOT NULL DEFAULT '3' COMMENT 'number of voting buttons',
  `btn_labels` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Stimme nicht zu,Nicht wichtig,Wichtig,Sehr wichtig,Super wichtig' COMMENT 'labels of voting buttons, comma-separated',
  PRIMARY KEY (`kid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
