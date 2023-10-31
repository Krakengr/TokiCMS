<?php defined('TOKICMS') or die('Hacking attempt...');

//
// Slug related Functions
//

#####################################################
#
# URLify a string function
#
#####################################################
function URLify( $string )
{
	include_once ( INC_ROOT . 'tools' . DS . 'urlify' . DS . 'URLify.php' );
	
	return URLify::filter( $string );
}

#####################################################
#
# Set a SEF key function (Generic)
#
# This function is being used to add a number in a string that already exists in the DB
# It can remove any short words if requested
#
#####################################################
function CreateSlug( $string, $letNum = false )
{
	global $Admin;
	
	$string = URLify( $string );
	
	$enable_seo = ( !$Admin ? Settings::IsTrue( 'enable_seo' ) : $Admin->Settings()::IsTrue( 'enable_seo' ) );

	if ( $enable_seo )
	{
		$seoSettings = ( !$Admin ? Settings::Seo() : $Admin->Settings()::Seo() );
	
		if ( !empty( $seoSettings ) && isset( $seoSettings['remove_short_words'] ) && is_numeric( $seoSettings['remove_short_words'] ) 
			&& ( $seoSettings['remove_short_words'] > 0 ) )
		{
			$temp = explode( '-', $string );
			
			$string = array();

			foreach ($temp as $k => $word) 
			{
				$num = (int) $word;
				
				if ( ( $letNum && is_numeric( $num ) ) || ( strlen( $word ) > $seoSettings['remove_short_words'] ) )
				{
					$string[] = $word;
				}
			}
			
			$string = implode('-', $string);
		}
    }

	//Remove double "-"
	$string = str_replace("--", "-", $string);
	
	return $string;
}

#####################################################
#
# Avoid duplicate strings function (Generic)
#
# This function is being used to add a number in a string, if it already exists in the DB
#
#####################################################
function SetShortSef( $where, $what, $key, $value, $id = null, $siteId = null, $hasSiteId = true, $langId = null, $maxSearch = 20 )
{
	global $Admin;
	
	//If we have sharing slugs enabled and the current lang is not the default, return the string as it is
	if ( $Admin && $Admin->Settings()::IsTrue( 'share_slugs' ) && ( $Admin->GetLang() != $Admin->DefaultLang()['id'] ) )
	{
		return $value;
	}
	
	$siteId = ( $siteId ? (int) $siteId : ( $Admin ? $Admin->GetSite() : SITE_ID ) );

	$db = db();
	
	$query = "
	SELECT " . $what . "
	FROM `" . DB_PREFIX . $where . "`
	WHERE (" . $key . " = :key)" . ( $id ? " AND (" . $what . " != " . (int) $id . ")" : "" ) . ( $hasSiteId ? " AND (id_site = " . $siteId . ")" : "" ) . ( $langId ? " AND (id_lang = " . (int) $langId . ")" : "" );
	
	//Query: slug
	$p = $db->from( null, $query, array( $value => ':key' ) )->single();

	//This slug doesn't exists, so return it as it is
	if ( !$p )
		return $value;
	
	//Loop through until we find a "clear" slug
	for ( $i = 1; $i < $maxSearch; $i++ )
	{
		$newKey = $value . '-' . $i;
		
		$query = "
		SELECT " . $what . "
		FROM `" . DB_PREFIX . $where . "`
		WHERE (" . $key . " = :key)" . ( $id ? "AND (" . $what . " != " . (int) $id . ")" : "" ) . ( $hasSiteId ? " AND (id_site = " . $siteId . ")" : "" ) . ( $langId ? " AND (id_lang = " . (int) $langId . ")" : "" );
		
		//Query: slug
		$s = $db->from( null, $query, array( $newKey => ':key' ) )->single();
		
		//If this key doesn't exist, return it
		if ( !$s )
		{
			return $newKey;
		}
	}
}

#####################################################
#
# Format a SEF key function
#
# TODO: REMOVE
#####################################################
function SeoFormatString( $string )
{
	$s = trim(mb_strtolower($string));
   
	$s = str_replace(
        array("ü", "ö", "ğ", "ı", "ə", "ç", "ş"), 
        array("u", "o", "g", "i", "e", "c", "s"), 
    $s);
	
	$cyr = array('а','б','в','г','д','е','ё','ж','з','и','й','к','л','м',
                'н','о','п','р','с','т','у', 'ф','х','ц','ч','ш','щ','ъ', 
                'ы','ь', 'э', 'ю','я');
	
	$lat = array('a','b','v','g','d','e','io','zh','z','i','y','k','l',
                'm','n','o','p','r','s','t','u', 'f', 'h', 'ts', 'ch',
                'sh', 'sht', 'a', 'i', 'y', 'e','yu', 'ya');

    $s = str_replace($cyr, $lat, $s);
    
	$unicode_vn = array("à","á","ạ","ả","ã","â","ầ","ấ","ậ","ẩ","ẫ","ă", "ằ","ắ","ặ","ẳ","ẵ","è","é","ẹ","ẻ","ẽ","ê","ề" ,"ế","ệ","ể","ễ", "ì","í","ị","ỉ","ĩ", "ò","ó","ọ","ỏ","õ","ô","ồ","ố","ộ","ổ","ỗ","ơ" ,"ờ","ớ","ợ","ở","ỡ", "ù","ú","ụ","ủ","ũ","ư","ừ","ứ","ự","ử","ữ", "ỳ","ý","ỵ","ỷ","ỹ", "đ", "À","Á","Ạ","Ả","Ã","Â","Ầ","Ấ","Ậ","Ẩ","Ẫ","Ă" ,"Ằ","Ắ","Ặ","Ẳ","Ẵ", "È","É","Ẹ","Ẻ","Ẽ","Ê","Ề","Ế","Ệ","Ể","Ễ", "Ì","Í","Ị","Ỉ","Ĩ", "Ò","Ó","Ọ","Ỏ","Õ","Ô","Ồ","Ố","Ộ","Ổ","Ỗ","Ơ" ,"Ờ","Ớ","Ợ","Ở","Ỡ", "Ù","Ú","Ụ","Ủ","Ũ","Ư","Ừ","Ứ","Ự","Ử","Ữ", "Ỳ","Ý","Ỵ","Ỷ","Ỹ", "Đ");
    
	$unicode_vn_latin = array("a","a","a","a","a","a","a","a","a","a","a" ,"a","a","a","a","a","a", "e","e","e","e","e","e","e","e","e","e","e", "i","i","i","i","i", "o","o","o","o","o","o","o","o","o","o","o","o" ,"o","o","o","o","o", "u","u","u","u","u","u","u","u","u","u","u", "y","y","y","y","y", "d", "a","a","a","a","a","a","a","a","a","a","a","a" ,"a","a","a","a","a", "e","e","e","e","e","e","e","e","e","e","e", "i","i","i","i","i", "o","o","o","o","o","o","o","o","o","o","o","o" ,"o","o","o","o","o", "u","u","u","u","u","u","u","u","u","u","u", "y","y","y","y","y", "d");
        
    $s = str_replace( $unicode_vn, $unicode_vn_latin, $s );
 
    $s = preg_replace( "/[^a-z0-9]/", "-", $s );
 
    $s = preg_replace( "/-{2,}/", "-", $s );

    return trim( $s, "-" );
}