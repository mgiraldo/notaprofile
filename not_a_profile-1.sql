-- phpMyAdmin SQL Dump
-- version 2.11.5
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 25-10-2009 a las 20:12:39
-- Versión del servidor: 5.0.51
-- Versión de PHP: 5.2.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Base de datos: `not_a_profile`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llave`
--

CREATE TABLE `llave` (
  `id` int(6) unsigned NOT NULL auto_increment,
  `codigo` varchar(10) character set utf8 collate utf8_unicode_ci NOT NULL,
  `txt` varchar(255) collate utf8_spanish_ci default NULL,
  `foto` varchar(40) character set utf8 collate utf8_unicode_ci default NULL,
  `fecha_creado` datetime NOT NULL,
  `latitud` float NOT NULL,
  `longitud` float NOT NULL,
  `fecha_reclamado` datetime default NULL,
  `creador_id` int(6) NOT NULL,
  `reclamador_id` int(6) default NULL,
  `flag_aceptado` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci AUTO_INCREMENT=1 ;

--
-- Volcar la base de datos para la tabla `llave`
--


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `id` int(6) unsigned NOT NULL auto_increment,
  `email` varchar(50) character set utf8 collate utf8_unicode_ci NOT NULL,
  `clave` varchar(32) character set utf8 collate utf8_unicode_ci NOT NULL,
  `flag_activo` tinyint(1) NOT NULL,
  `fecha_creado` datetime NOT NULL,
  `id_activacion` varchar(35) character set utf8 collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci AUTO_INCREMENT=1 ;

--
-- Volcar la base de datos para la tabla `usuario`
--

