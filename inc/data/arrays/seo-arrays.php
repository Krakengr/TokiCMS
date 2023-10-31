<?php defined('TOKICMS') or die('Hacking attempt...');

global $L;

#####################################################
#
# Category Custom Meta Format Array
#
#####################################################
$categoryCustomMetaFormat = array(
	'category-title' => array( 'id' => 'category-title-format', 'value'=> $L['category-title'] ),
	'category-description' => array( 'id' => 'category-description-format', 'value'=> $L['category-description'] ),
	'site-title' => array( 'id' => 'category-site-title-format', 'value'=> $L['site-title'] ),
	'site-slogan' => array( 'id' => 'category-site-slogan-format', 'value'=> $L['site-slogan'] ),
	'site-description' => array( 'id' => 'category-site-description-format', 'value'=> $L['site-description'] ),
	'page-number' => array( 'id' => 'category-page-number-format', 'value'=> $L['page-number'] ),
	'post-count' => array( 'id' => 'post-count', 'value'=> $L['post-count'] ),
	'sep' => array( 'id' => 'category-sep-format', 'value'=> $L['seperator'] ),
	//'num-prices' => array( 'id' => 'category-num-prices-format', 'value'=> $L['number-of-prices'] ),
	//'best-price' => array( 'id' => 'category-best-price-format', 'value'=> $L['best-price'] ),
);

#####################################################
#
# indexNow Search Engines Array
#
#####################################################
$indexNowSearchEngines = array(
	'bing' => array( 'name' => 'bing', 'title'=> __( 'bing' ), 'url' => 'https://www.bing.com/indexnow?url={{url}}&key={{key}}' ),
	'seznam' => array( 'name' => 'seznam', 'title'=> __( 'seznam' ), 'url' => 'https://search.seznam.cz/indexnow?url={{url}}&key={{key}}' ),
	'yandex' => array( 'name' => 'yandex', 'title'=> __( 'yandex' ), 'url' => 'https://yandex.com/indexnow?url={{url}}&key={{key}}' ),
);

#####################################################
#
# URL Response Codes Array
#
#####################################################
$urlResponseCodes = array(
		'200' => array( 'name' => '200', 'title'=> '200 OK' ),
		'201' => array( 'name' => '201', 'title'=> '201 Created' ),
		'202' => array( 'name' => '202', 'title'=> '202 Accepted' ),
		'203' => array( 'name' => '203', 'title'=> '203 Non-Authoritative Information' ),
		'204' => array( 'name' => '204', 'title'=> '204 No Content' ),
		'205' => array( 'name' => '205', 'title'=> '205 Reset Content' ),
		'206' => array( 'name' => '206', 'title'=> '206 Partial Content' ),
		'300' => array( 'name' => '300', 'title'=> '300 Multiple Choices' ),
		'301' => array( 'name' => '301', 'title'=> '301 Moved Permanently' ),
		'302' => array( 'name' => '302', 'title'=> '302 Found / Moved Temporarily' ),
		'303' => array( 'name' => '303', 'title'=> '303 See Other' ),
		'304' => array( 'name' => '304', 'title'=> '304 Not Modified' ),
		'307' => array( 'name' => '307', 'title'=> '307 Moved Temporarily' ),
		'308' => array( 'name' => '308', 'title'=> '308 Permanent Redirect' ),
		'400' => array( 'name' => '400', 'title'=> '400 Bad Request' ),
		'401' => array( 'name' => '401', 'title'=> '401 Unauthorized' ),
		'403' => array( 'name' => '403', 'title'=> '403 Forbidden' ),
		'404' => array( 'name' => '404', 'title'=> '404 Not Found' ),
		'405' => array( 'name' => '405', 'title'=> '405 Method Not Allowed' ),
		'406' => array( 'name' => '406', 'title'=> '406 Not Acceptable' ),
		'407' => array( 'name' => '407', 'title'=> '407 Proxy Authentication Required' ),
		'408' => array( 'name' => '408', 'title'=> '408 Request Timeout' ),
		'409' => array( 'name' => '409', 'title'=> '409 Conflict' ),
		'410' => array( 'name' => '410', 'title'=> '410 Gone' ),
		'411' => array( 'name' => '411', 'title'=> '411 Length Required' ),
		'412' => array( 'name' => '412', 'title'=> '412 Precondition Failed' ),
		'413' => array( 'name' => '413', 'title'=> '413 Payload Too Large' ),
		'414' => array( 'name' => '414', 'title'=> '414 URI Too Long' ),
		'415' => array( 'name' => '415', 'title'=> '415 Unsupported Media Type' ),
		'416' => array( 'name' => '416', 'title'=> '416 Range Not Satisfiable' ),
		'417' => array( 'name' => '417', 'title'=> '417 Expectation Failed' ),
		'429' => array( 'name' => '429', 'title'=> '429 Too Many Requests' ),
		'431' => array( 'name' => '431', 'title'=> '431 Request Header Fields Too Large' ),
		'451' => array( 'name' => '451', 'title'=> '451 Unavailable For Legal Reasons' ),
		'500' => array( 'name' => '500', 'title'=> '500 Internal Server Error' ),
		'501' => array( 'name' => '501', 'title'=> '501 Not Implemented' ),
		'502' => array( 'name' => '502', 'title'=> '502 Bad Gateway' ),
		'503' => array( 'name' => '503', 'title'=> '503 Service Unavailable' ),
		'504' => array( 'name' => '504', 'title'=> '504 Gateway Timeout' ),
		'505' => array( 'name' => '505', 'title'=> '505 HTTP Version Not Supported' ),
		'506' => array( 'name' => '506', 'title'=> '506 Variant Also Negotiates' ),
		'510' => array( 'name' => '510', 'title'=> '510 Not Extended' ),
		'511' => array( 'name' => '511', 'title'=> '511 Network Authentication Required' )
);	

#####################################################
#
# URL Redirection Type Array
#
#####################################################
$urlRedirectionTypeArray = array(
	'301' => array( 'name' => '301', 'title'=> '301 Moved Permanently' ),
	'302' => array( 'name' => '302', 'title'=> '302 Found / Moved Temporarily' ),
	'307' => array( 'name' => '307', 'title'=> '307 Moved Temporarily' ),
	'410' => array( 'name' => '410', 'title'=> '410 Gone' ),
	'451' => array( 'name' => '451', 'title'=> '451 Unavailable For Legal Reasons' )
);

#####################################################
#
# Title Seperator Array
#
#####################################################
$titleSeperatorArray = array('dash' => array( 'title' => $L['dash'] , 'code' => '-' ),'endash' => array( 'title' => $L['endash'] , 'code' => '&ndash;' ),'emdash' => array( 'title' => $L['emdash'] , 'code' => '&mdash;' ),'colon' => array( 'title' => $L['colon'] , 'code' => ':' ),'middle-dot' => array( 'title' => $L['middle-dot'] , 'code' => '&middot;' ),
'bullet' => array( 'title' => $L['bullet'] , 'code' => '&bull;' ),'asterisk' => array( 'title' => $L['asterisk'] , 'code' => '*' ),'low-asterisk' => array( 'title' => $L['low-asterisk'] , 'code' => '&#8902;' ),'vertical-bar' => array( 'title' => $L['vertical-bar'] , 'code' => '|' ),'small-tilde' => array( 'title' => $L['small-tilde'] , 'code' => '~' ),'left-angle' => array( 'title' => $L['left-angle'] , 'code' => '&laquo;' ),'right-angle' => array( 'title' => $L['right-angle'] , 'code' => '&raquo;' ),'less-than-sign' => array( 'title' => $L['less-than-sign'] , 'code' => '&lt;' ),'greater-than-sign' => array( 'title' => $L['greater-than-sign'] , 'code' => '&gt;' ),'broken-vertical-bar' => array( 'title' => $L['broken-vertical-bar'] , 'code' => '&brvbar;' ) );

#####################################################
#
# Schema Event Types Array
#
#####################################################
$schemaEventTypesArray = array(
	'event' => array( 'name' => 'event', 'property' => 'Event', 'title'=> $L['event'] ),
	'business-event' => array( 'name' => 'business-event', 'property' => 'BusinessEvent', 'title'=> $L['business-event'] ),
	'childrens-event' => array( 'name' => 'childrens-event', 'property' => 'ChildrensEvent', 'title'=> $L['childrens-event'] ),
	'comedy-event' => array( 'name' => 'comedy-event', 'property' => 'ComedyEvent', 'title'=> $L['comedy-event'] ),
	'course-instance' => array( 'name' => 'course-instance', 'property' => 'CourseInstance', 'title'=> $L['course-instance'] ),
	'dance-event' => array( 'name' => 'dance-event', 'property' => 'DanceEvent', 'title'=> $L['dance-event'] ),
	'delivery-event' => array( 'name' => 'delivery-event', 'property' => 'DeliveryEvent', 'title'=> $L['delivery-event'] ),
	'education-event' => array( 'name' => 'education-event', 'property' => 'EducationEvent', 'title'=> $L['education-event'] ),
	'event-series' => array( 'name' => 'event-series', 'property' => 'EventSeries', 'title'=> $L['event-series'] ),
	'exhibition-event' => array( 'name' => 'exhibition-event', 'property' => 'ExhibitionEvent', 'title'=> $L['exhibition-event'] ),
	'festival' => array( 'name' => 'festival', 'property' => 'Festival', 'title'=> $L['festival'] ),
	'food-event' => array( 'name' => 'food-event', 'property' => 'FoodEvent', 'title'=> $L['food-event'] ),
	'literary-event' => array( 'name' => 'literary-event', 'property' => 'LiteraryEvent', 'title'=> $L['literary-event'] ),
	'music-event' => array( 'name' => 'music-event', 'property' => 'MusicEvent', 'title'=> $L['music-event'] ),
	'publication-event' => array( 'name' => 'publication-event', 'property' => 'PublicationEvent', 'title'=> $L['publication-event'] ),
	'sale-event' => array( 'name' => 'sale-event', 'property' => 'SaleEvent', 'title'=> $L['sale-event'] ),
	'screening-event' => array( 'name' => 'screening-event', 'property' => 'ScreeningEvent', 'title'=> $L['screening-event'] ),
	'social-event' => array( 'name' => 'social-event', 'property' => 'SocialEvent', 'title'=> $L['social-event'] ),
	'sports-event' => array( 'name' => 'sports-event', 'property' => 'SportsEvent', 'title'=> $L['sports-event'] ),
	'theater-event' => array( 'name' => 'theater-event', 'property' => 'TheaterEvent', 'title'=> $L['theater-event'] ),
	'visual-arts-event' => array( 'name' => 'visual-arts-event', 'property' => 'VisualArtsEvent', 'title'=> $L['visual-arts-event'] )
);
#####################################################
#
# Schema Types Array
#
#####################################################
$schemaTypesArray = array(
	'article' => array( 'name' => 'article', 'property' => 'Article', 'title'=> $L['article'] ),
	'book' => array( 'name' => 'book', 'property' => 'Book', 'title'=> $L['book'] ),
	'claim-review' => array( 'name' => 'claim-review', 'property' => 'ClaimReview', 'title'=> $L['claim-review'] ),
	'course' => array( 'name' => 'course', 'property' => 'course', 'title'=> $L['course'] ),
	'event' => array( 'name' => 'event', 'property' => 'Event', 'title'=> $L['event'] ),
	'faq' => array( 'name' => 'faq', 'property' => 'FAQPage', 'title'=> $L['faq'] ),
	'how-to' => array( 'name' => 'how-to', 'property' => 'HowTo', 'title'=> $L['how-to'] )
	
	//TODO:
				//'videogame' => array( 'name' => 'videogame', 'property' => 'VideoGame', 'title'=> $L['videogame'] ),
				//'job-posting' => array( 'name' => 'job-posting', 'property' => 'JobPosting', 'title'=> $L['job-posting'] ),
				//'local-business' => array( 'name' => 'local-business', 'property' => 'LocalBusiness', 'title'=> $L['local-business'] ),
				//'review' => array( 'name' => 'review', 'property' => 'Review', 'title'=> $L['review'] ),
				//'person' => array( 'name' => 'person', 'property' => 'Person', 'title'=> $L['person'] ),
				//'product' => array( 'name' => 'product', 'property' => 'Product', 'title'=> $L['product'] ),
				//'recipe' => array( 'name' => 'recipe', 'property' => 'Recipe', 'title'=> $L['recipe'] ),
				//'service' => array( 'name' => 'service', 'property' => 'Service', 'title'=> $L['service'] ),
				//'software-application' => array( 'name' => 'software-application', 'property' => 'SoftwareApplication', 'title'=> $L['software-application'] ),
				//'video-object' => array( 'name' => 'video-object', 'property' => 'VideoObject', 'title'=> $L['video-object'] ),
);

#####################################################
#
# Schema Article Types Array
#
#####################################################
$schemaArticleTypesArray = array(
	'article' => array( 'name' => 'article', 'property' => 'Article', 'title'=> $L['article-general'] ),
	'advertiser-content-article' => array( 'name' => 'advertiser-content-article', 'property' => 'AdvertiserContentArticle', 'title'=> $L['advertiser-content-article'] ),
	'blog-posting' => array( 'name' => 'blog-posting', 'property' => 'BlogPosting', 'title'=> $L['blog-posting'] ),
	'news-article' => array( 'name' => 'news-article', 'property' => 'NewsArticle', 'title'=> $L['news-article'] ),
	'report' => array( 'name' => 'report', 'property' => 'Report', 'title'=> $L['report'] ),
	'satirical-article' => array( 'name' => 'satirical-article', 'property' => 'SatiricalArticle', 'title'=> $L['satirical-article'] ),
	'scholarly-article' => array( 'name' => 'scholarly-article', 'property' => 'ScholarlyArticle', 'title'=> $L['scholarly-article'] ),
	'tech-article' => array( 'name' => 'tech-article', 'property' => 'TechArticle', 'title'=> $L['tech-article'] )
);

#####################################################
#
# Book Format Array
#
#####################################################
$schemaBookFormatArray = array(
	'ebook' => array( 'name' => 'ebook', 'property' => 'EBook', 'title'=> $L['ebook'] ),
	'hardcover' => array( 'name' => 'hardcover', 'property' => 'Hardcover', 'title'=> $L['hardcover'] ),
	'paperback' => array( 'name' => 'paperback', 'property' => 'Paperback', 'title'=> $L['paperback'] ),
	'audiobook' => array( 'name' => 'audiobook', 'property' => 'AudioBook', 'title'=> $L['audiobook'] )
);

#####################################################
#
# Course Status Array
#
#####################################################
$schemaCourseStatusArray = array(
	'scheduled' => array( 'name' => 'scheduled', 'property' => 'EventScheduled', 'title'=> $L['scheduled'] ),
	'rescheduled' => array( 'name' => 'rescheduled', 'property' => 'EventRescheduled', 'title'=> $L['rescheduled'] ),
	'postponed' => array( 'name' => 'postponed', 'property' => 'EventPostponed', 'title'=> $L['postponed'] ),
	'moved-online' => array( 'name' => 'moved-online', 'property' => 'EventMovedOnline', 'title'=> $L['moved-online'] ),
	'cancelled' => array( 'name' => 'cancelled', 'property' => 'EventCancelled', 'title'=> $L['cancelled'] )
);

#####################################################
#
# Course Attendance Mode Array
#
#####################################################
$schemaAttendanceModeArray = array(
	'physical-location' => array( 'name' => 'physical-location', 'property' => 'OfflineEventAttendanceMode', 'title'=> $L['physical-location'] ),
	'online-event' => array( 'name' => 'online-event', 'property' => 'OnlineEventAttendanceMode', 'title'=> $L['online-event'] ),
	'mixed-event-attendance-mode' => array( 'name' => 'mixed-event-attendance-mode', 'property' => 'MixedEventAttendanceMode', 'title'=> $L['mixed-event-attendance-mode'] )
);

#####################################################
#
# Availability Array
#
#####################################################
$schemaAvailabilityArray = array(
	'discontinued' => array( 'name' => 'discontinued', 'property' => 'Discontinued', 'title'=> $L['discontinued'] ),
	'in-stock' => array( 'name' => 'in-stock', 'property' => 'InStock', 'title'=> $L['in-stock'] ),
	'in-store-only' => array( 'name' => 'in-store-only', 'property' => 'InStoreOnly', 'title'=> $L['in-store-only'] ),
	'limited-availability' => array( 'name' => 'limited-availability', 'property' => 'LimitedAvailability', 'title'=> $L['limited-availability'] ),
	'online-only' => array( 'name' => 'online-only', 'property' => 'OnlineOnly', 'title'=> $L['online-only'] ),
	'out-of-stock' => array( 'name' => 'out-of-stock', 'property' => 'OutOfStock', 'title'=> $L['out-of-stock'] ),
	'pre-order' => array( 'name' => 'pre-order', 'property' => 'PreOrder', 'title'=> $L['pre-order'] ),
	'pre-sale' => array( 'name' => 'pre-sale', 'property' => 'PreSale', 'title'=> $L['pre-sale'] ),
	'sold-out' => array( 'name' => 'sold-out', 'property' => 'SoldOut', 'title'=> $L['sold-out'] )
);
