-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 18-10-2021 a las 01:02:30
-- Versión del servidor: 5.7.31
-- Versión de PHP: 7.3.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `practica`
--
CREATE DATABASE IF NOT EXISTS `practica` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `practica`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `descripciones`
--

DROP TABLE IF EXISTS `descripciones`;
CREATE TABLE IF NOT EXISTS `descripciones` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `termino` text NOT NULL,
  `NomDoc` text NOT NULL,
  `descripcion` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `descripciones`
--

INSERT INTO `descripciones` (`id`, `termino`, `NomDoc`, `descripcion`) VALUES
(1, 'ir', 'archivo1', 'ir agregando noticia'),
(2, 'agregando', 'archivo1', 'ir agregando noticia'),
(3, 'noticia', 'archivo1', 'ir agregando noticia'),
(4, 'este', 'testText', 'Este es un ejemplo de archivo para intro'),
(5, 'es', 'testText', 'Este es un ejemplo de archivo para intro'),
(6, 'un', 'testText', 'Este es un ejemplo de archivo para intro'),
(7, 'ejemplo', 'testText', 'Este es un ejemplo de archivo para intro'),
(8, 'de', 'testText', 'Este es un ejemplo de archivo para intro'),
(9, 'archivo', 'testText', 'Este es un ejemplo de archivo para intro'),
(10, 'para', 'testText', 'Este es un ejemplo de archivo para intro'),
(11, 'introducir', 'testText', 'Este es un ejemplo de archivo para intro'),
(12, 'lorem', 'testText', 'Este es un ejemplo de archivo para intro'),
(13, 'ipsum', 'testText', 'Este es un ejemplo de archivo para intro');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tablafrecuencias2`
--

DROP TABLE IF EXISTS `tablafrecuencias2`;
CREATE TABLE IF NOT EXISTS `tablafrecuencias2` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `termino` text NOT NULL,
  `frecuenciaTotal` bigint(20) NOT NULL,
  `documentos` text NOT NULL,
  `archivo1` int(11) DEFAULT NULL,
  `archivo10` int(11) DEFAULT NULL,
  `archivo2` int(11) DEFAULT NULL,
  `archivo3` int(11) DEFAULT NULL,
  `archivo4` int(11) DEFAULT NULL,
  `archivo5` int(11) DEFAULT NULL,
  `archivo6` int(11) DEFAULT NULL,
  `archivo7` int(11) DEFAULT NULL,
  `archivo8` int(11) DEFAULT NULL,
  `archivo9` int(11) DEFAULT NULL,
  `testText` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `tablafrecuencias2`
--

INSERT INTO `tablafrecuencias2` (`id`, `termino`, `frecuenciaTotal`, `documentos`, `archivo1`, `archivo10`, `archivo2`, `archivo3`, `archivo4`, `archivo5`, `archivo6`, `archivo7`, `archivo8`, `archivo9`, `testText`) VALUES
(1, 'ir', 1, '1', 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(2, 'agregando', 1, '1', 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(3, 'noticia', 1, '1', 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(4, 'este', 1, '1', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1),
(5, 'es', 1, '1', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1),
(6, 'un', 1, '1', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1),
(7, 'ejemplo', 1, '1', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1),
(8, 'de', 1, '1', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1),
(9, 'archivo', 1, '1', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1),
(10, 'para', 1, '1', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1),
(11, 'introducir', 1, '1', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1),
(12, 'lorem', 1, '1', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1),
(13, 'ipsum', 1, '1', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
