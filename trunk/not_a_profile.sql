-- phpMyAdmin SQL Dump
-- version 3.2.0.1
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 22-10-2009 a las 17:08:36
-- Versión del servidor: 5.1.36
-- Versión de PHP: 5.3.0

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `notaprofile`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llave`
--

CREATE TABLE IF NOT EXISTS `llave` (
  `id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `codigo` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `txt` varchar(255) COLLATE utf8_spanish_ci DEFAULT NULL,
  `foto` varchar(40) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `fecha_creado` datetime NOT NULL,
  `latitud` float NOT NULL,
  `longitud` float NOT NULL,
  `fecha_reclamado` datetime DEFAULT NULL,
  `creador_id` int(6) NOT NULL,
  `reclamador_id` int(6) DEFAULT NULL,
  `flag_aceptado` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci AUTO_INCREMENT=16 ;

--
-- Volcar la base de datos para la tabla `llave`
--

INSERT INTO `llave` (`id`, `codigo`, `txt`, `foto`, `fecha_creado`, `latitud`, `longitud`, `fecha_reclamado`, `creador_id`, `reclamador_id`, `flag_aceptado`) VALUES
(13, 'faebe4', '', NULL, '2009-10-13 23:20:14', 4.61116, -74.0624, NULL, 1, NULL, 0),
(14, 'c7e4da', 'Llave de Prueba', NULL, '2009-10-13 23:23:41', 4.62348, -74.0925, NULL, 1, NULL, 0),
(15, 'f95236', 'La llave estÃ¡ en ciudad universitaria', NULL, '2009-10-13 23:39:22', 4.63614, -74.0837, NULL, 1, NULL, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE IF NOT EXISTS `usuario` (
  `id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `clave` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `flag_activo` tinyint(1) NOT NULL,
  `fecha_creado` datetime NOT NULL,
  `token_validacion` varchar(50) COLLATE utf8_spanish_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci AUTO_INCREMENT=6 ;

--
-- Volcar la base de datos para la tabla `usuario`
--

