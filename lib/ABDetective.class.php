<?php
/**
 * ABDetective - Ad Block Detective
 * Copyright (c) 2011 Heiko Irrgang
 *
 * This file is part of Ad Block Detective.
 * 
 * Ad Block Detective is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * FAd Block Detective is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with Ad Block Detective. If not, see <http://www.gnu.org/licenses/>.
 */

class ABDetective {
	private static $whiteList = array(
		'/\.googlebot\.com$/',
		'/\.crawl\.yahoo\.net$/',
		'/\.search\.msn\.com$/',
	);

	private static $ipWhiteList = array(
		'/^72\.94\.249\.3[4-8]$/', // http://duckduckgo.com/duckduckbot.html
	);

	private static $adURL = 'iframead.html';
	private static $browser = 'Unknown';

	private static $messageDiv;
	private static $buttonDiv;
	private static $contentClass;
	private static $functionPrefix;
	private static $startPrefix;

	public static function modeBlock( $buf ) {
		return self::modeExecute( $buf, 'Block' );
	}

	public static function modeRedirect( $buf ) {
		return self::modeExecute( $buf, 'Redirect' );
	}

	public static function modeShareware( $buf ) {
		return self::modeExecute( $buf, 'Shareware' );
	}

	private static function modeExecute( $buf, $mode ) {
		if ( self::isWhiteListed() ) {
			return $buf;
		}

		self::setVariables( $buf, $mode );
		$qf = self::getQueryFile();
		switch ( $qf ) {
		case self::$adURL:
			header( 'HTTP/1.1: 200 OK' );
			return self::getIFrame();
		case self::$infoName:
			header( 'HTTP/1.1: 200 OK' );
			return self::getInfo( $mode );
		}

		return self::MainJS( $buf, $mode );
	}

	private static function setVariables( $buf, $mode ) {
		srand( time() );
		self::$messageDiv = self::generateRandom( $buf );
		self::$contentClass = self::generateRandom( $buf, array( self::$messageDiv ) );
		self::$functionPrefix = self::generateRandom( $buf, array( self::$messageDiv, self::$contentClass ) );
		self::$buttonDiv = self::generateRandom( $buf, array( self::$messageDiv, self::$contentClass, self::$functionPrefix ) );
		self::$startPrefix = preg_replace( '/[^a-zA-Z]/', '', $_SERVER['SERVER_NAME'] );
	}

	private static $redirectURL = '';
	public static function setRedirectURL( $url ) {
		self::$redirectURL = $url;
	}

	private static $infoName = '';
	public static function setInfoName( $name ) {
		self::$infoName = $name;
	}

	public static function generateRandom( $buf, $used = array() ) {
		$found = false;
		$amount = 0;
		$str = '';
		while ( ( !$found ) && ( $amount < 100 ) ) {
			$len = rand( 4, 16 );
			$str = '';
			for ( $i=0; $i<$len; $i++ ) {
				$str.=chr( rand( (int) ord( 'a' ), (int) ord( 'z' ) ) );
			}
			if (
				( strpos( $buf, $str ) === false ) &&
				( array_search( $str, $used ) === false )
			) {
				$found = true;
			}
			$amount++;
		}
		if ( !$found ) {
			return 'maxlimitreached';
		}
		return $str;
	}

	private static function MainJS( $page, $mode ) {
		$src = array();
		$dst = array();

		$src[] = '/<[^<>]*\/\s*body\s*>/i';
		$dst[] = sprintf( '%s\0', self::getEndBlock() );

		$src[] = '/<\s*body[^>]*>/i';
		$dst[] = sprintf( '\0%s', self::getMainJS( $mode ) );

		return preg_replace( $src, $dst, preg_replace_callback( '/<\s*div[^>]*>/i', 'self::divCallback', $page, 1 ) );
	}

	private static function divCallback( $matches ) {
		$src = array();
		$dst = array();

		if ( preg_match( '/\s+class\s*=/i', $matches[0] ) ) {
			$src[] = '/\s+class\s*=\s*([\'"])/i';
			$dst[] = sprintf( ' class=\1%s ', self::$contentClass );
		} else {
			$src[] = '/div/i';
			$dst[] = sprintf( 'div class="%s"', self::$contentClass );
		}

		if ( preg_match( '/\s+style\s*=/i', $matches[0] ) ) {
			$src[] = '/\s+style\s*=\s*([\'"])/i';
			$dst[] = ' style=\1visibility: hidden; ';
		} else {
			$src[] = '/div/i';
			$dst[] = 'div style="visibility: hidden;"';
		}

		return preg_replace( $src, $dst, $matches[0] );
	}

	private static function isWhiteListed() {
		$ip = $_SERVER['REMOTE_ADDR'];
		foreach ( self::$ipWhiteList as $wi ) {
			if ( preg_match( $wi, $ip ) ) {
				return true;
			}
		}
		$host = gethostbyaddr( $ip );
		foreach( self::$whiteList as $wl ) {
			if ( preg_match( $wl, $host ) ) {
				return true;
			}
		}
		return false;
	}

	public static function getQueryFile() {
		$parts = parse_url( $_SERVER['REQUEST_URI'] );
		if ( isset( $parts['path'] ) ) {
			$tmp = explode( '/', $parts['path'] );
			return $tmp[sizeof( $tmp ) -1];
		}
		return '';
	}

	private static function getMainJS( $mode ) {
		return self::returnReplaced(
			str_replace( 
				'{modeCode}',
				file_get_contents( sprintf( '%s/data/mode%s.js', self::getPluginDir(), $mode ) ),
				file_get_contents( sprintf( '%s/data/main.html', self::getPluginDir() ) )
			)
	       	);
	}

	private static function getIFrame() {
		return self::returnReplaced( file_get_contents( sprintf( '%s/data/iframe.html', self::getPluginDir() ) ) );
	}

	private static function getEndBlock() {
		$ret = array();
		$ret[] = sprintf( '<iframe style="display: none;" src="%s/%s"></iframe>', site_url(), self::$adURL );
		$ret[] = file_get_contents( sprintf( '%s/data/opera.html', self::getPluginDir() ) );

		return self::returnReplaced( join( "\n", $ret ) );
	}

	private static $pluginDir = '';
	private static function getPluginDir() {
		return self::$pluginDir;
	}

	public static function setPluginDir( $dir ) {
		self::$pluginDir = $dir;
	}

	private static function returnReplaced( $str ) {
		$src = array();
		$dst = array();

		$src[] = '{messageDiv}';
		$dst[] = self::$messageDiv;

		$src[] = '{buttonDiv}';
		$dst[] = self::$buttonDiv;

		$src[] = '{contentClass}';
		$dst[] = self::$contentClass;

		$src[] = '{functionPrefix}';
		$dst[] = self::$functionPrefix;

		$src[] = '{startPrefix}';
		$dst[] = self::$startPrefix;

		$src[] = '{redirectURL}';
		$dst[] = htmlspecialchars( self::$redirectURL );

		$src[] = '{infoURL}';
		$dst[] = htmlspecialchars( self::$infoName );

		return str_replace( $src, $dst, $str );
	}

	private static function getInfo( $mode ) {
		switch ( $mode ) {
		case 'Shareware':
			$file = get_option( 'abdetective_sharewarefile', 'default.shareware.html' );
			if ( preg_match( '/^[^\.\/]/', $file ) ) {
				$num = rand(1, 5);
				$src = array();
				$dst = array();
				$text = '';
				for ( $i = 0 ; $i < 5; $i++ ) {
					if ( $i < $num ) {
						$text.=' '.chr( rand( (int) ord( 'A' ), (int) ord( 'Z' ) ) );
					}
					$src[] = sprintf( '{handler%d}', $i+1 );
					if ( $i == $num-1 ) {
						$dst[] = sprintf( 'onclick="%sStart();"', self::$startPrefix );
					} else {
						$dst[] = '';
					}
				}
				$buttons = str_replace( $src, $dst, self::returnReplaced( file_get_contents( sprintf( '%s/data/shareware.html', self::getPluginDir() ) ) ) );
				$info = str_replace( '{RESULT}', $text, file_get_contents( sprintf( '%s/data/%s', self::getPluginDir(), $file ) ) );
				return $info.$buttons;
			}
		default:
			$file = get_option( 'abdetective_blockfile', 'default.block.html' );
			if ( preg_match( '/^[^\.\/]/', $file ) ) {
				return file_get_contents( sprintf( '%s/data/%s', self::getPluginDir(), $file ) );
			}
		}
	}
}
