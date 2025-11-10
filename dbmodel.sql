-- ------
-- BGA framework: Gregory Isabelli & Emmanuel Colin & BoardGameArena
-- DeadMenPax implementation : © <Your name here> <Your email address here>
-- 
-- This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
-- See http://en.boardgamearena.com/#!doc/Studio for more information.
-- -----

-- dbmodel.sql

-- This is the file where you are describing the database schema of your game
-- Basically, you just have to export from PhpMyAdmin your table structure and copy/paste
-- this export here.
-- Note that the database itself and the standard tables ("global", "stats", "gamelog" and "player") are
-- already created and must not be created here

-- Note: The database schema is created from this file when the game starts. If you modify this file,
--       you have to restart a game to see your changes in database.

-- Add custom fields to the standard "player" table
ALTER TABLE `player` ADD `player_fatigue` INT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `player` ADD `player_battle_strength` INT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `player` ADD `player_room_x` INT NOT NULL DEFAULT '-1';
ALTER TABLE `player` ADD `player_room_y` INT NOT NULL DEFAULT '-1';
ALTER TABLE `player` ADD `player_is_on_ship` TINYINT(1) NOT NULL DEFAULT '0';
ALTER TABLE `player` ADD `player_actions_remaining` INT UNSIGNED NOT NULL DEFAULT '5';
ALTER TABLE `player` ADD `player_max_actions` INT UNSIGNED NOT NULL DEFAULT '5';
ALTER TABLE `player` ADD `player_extra_actions` INT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `player` ADD `player_character_card_id` INT NULL;
ALTER TABLE `player` ADD `player_item_card_id` INT NULL;
ALTER TABLE `player` ADD `player_current_enemy_token_id` VARCHAR(64) NULL;
ALTER TABLE `player` ADD `player_current_battle_room_id` INT NULL;
ALTER TABLE `player` ADD `player_battle_state` VARCHAR(32) NULL;

-- Separate card tables for different card management needs

-- Skelit Revenge Cards - BGA DECK component with auto-reshuffle (19 cards)
CREATE TABLE IF NOT EXISTS `skelit_card` (
  `card_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `card_type` varchar(16) NOT NULL,
  `card_type_arg` int(11) NOT NULL,
  `card_location` varchar(16) NOT NULL,
  `card_location_arg` int(11) NOT NULL,
  PRIMARY KEY (`card_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- Character Cards - BGA DECK component without reshuffle (7 cards)
CREATE TABLE IF NOT EXISTS `character_card` (
  `card_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `card_type` varchar(16) NOT NULL,
  `card_type_arg` int(11) NOT NULL,
  `card_location` varchar(16) NOT NULL,
  `card_location_arg` int(11) NOT NULL,
  PRIMARY KEY (`card_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- Item Cards - Custom management for swapping (7 cards)
CREATE TABLE IF NOT EXISTS `item_card` (
  `card_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `card_type` varchar(16) NOT NULL,
  `card_type_arg` int(11) NOT NULL,
  `card_location` varchar(16) NOT NULL,
  `card_location_arg` int(11) NOT NULL,
  PRIMARY KEY (`card_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- Room tiles table to track ship layout and fire levels
CREATE TABLE IF NOT EXISTS `room_tile` (
  `tile_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tile_type` varchar(16) NOT NULL,
  `x` int(11) DEFAULT NULL,
  `y` int(11) DEFAULT NULL,
  `fire_level` int(11) NOT NULL DEFAULT '0',
  `deckhand_count` int(11) NOT NULL DEFAULT '0',
  `has_powder_keg` tinyint(1) NOT NULL DEFAULT '0',
  `powder_keg_exploded` tinyint(1) NOT NULL DEFAULT '0',
  `is_exploded` tinyint(1) NOT NULL DEFAULT '0',
  `doors` int(11) NOT NULL DEFAULT '0',
  `orientation` int(11) NOT NULL DEFAULT '0',
  `color` varchar(16) NOT NULL DEFAULT 'red',
  `pips` int(11) NOT NULL DEFAULT '1',
  `has_trapdoor` tinyint(1) NOT NULL DEFAULT '0',
  `is_starting_tile` tinyint(1) NOT NULL DEFAULT '0',
  `tile_order` int(11) DEFAULT NULL,
  PRIMARY KEY (`tile_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- Token table for all game tokens (Treasure/Guard, Skeleton Crew, etc.)
CREATE TABLE IF NOT EXISTS `token` (
  `token_id` varchar(32) NOT NULL,
  `token_type` varchar(16) NOT NULL,
  `token_location` varchar(32) NOT NULL,
  `token_location_arg` int(11) NOT NULL DEFAULT '0',
  `token_state` int(11) NOT NULL DEFAULT '0',
  `token_order` int(11) NOT NULL DEFAULT '0',
  `front_type` varchar(16) NOT NULL DEFAULT '',
  `front_value` int(11) NULL,
  `back_type` varchar(16) NULL,
  `back_value` int(11) NULL,
  PRIMARY KEY (`token_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

-- Deckhand table to track skeleton crew deckhands
CREATE TABLE IF NOT EXISTS `deckhand` (
  `deckhand_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `room_x` int(11) NOT NULL,
  `room_y` int(11) NOT NULL,
  PRIMARY KEY (`deckhand_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- Fire dice table to track fire levels in rooms
CREATE TABLE IF NOT EXISTS `fire_die` (
  `die_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `room_x` int(11) NOT NULL,
  `room_y` int(11) NOT NULL,
  `color` varchar(16) NOT NULL,
  `value` int(11) NOT NULL,
  PRIMARY KEY (`die_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


-- Game state table for tracking global game variables
CREATE TABLE IF NOT EXISTS `game_state` (
  `state_key` varchar(32) NOT NULL,
  `state_value` text NOT NULL,
  PRIMARY KEY (`state_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;
