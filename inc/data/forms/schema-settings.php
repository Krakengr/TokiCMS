<?php defined('TOKICMS') or die('Hacking attempt...');
#####################################################
#
# Schema Settings Form
#
#####################################################
$L = $this->lang;

$settings = $this->adminSettings::Get();

include ( ARRAYS_ROOT . 'seo-arrays.php');
include ( ARRAYS_ROOT . 'generic-arrays.php');

$schema = Json( $settings['schema_data'] );

//$socialMedia = $schema['social-media'];

$contactPage = ( ( isset( $schema['contact_page'] ) && !empty( $schema['contact_page'] ) ) ? Json( $schema['contact_page'] ) : null );

$organizationType = $contactType = $breadcrumbPostsData = $pagesData = $socialData = array();

//Add values to the "organizationType" array
foreach( $organizationTypes as $id => $row )
	$organizationType[$id] = array( 'name' => $id, 'title'=> $row['title'], 'disabled' => false, 'data' => array() );

//Add values to the "breadcrumbPostsData" array
$breadcrumbPostsData['disable'] = array( 'name' => 'disable', 'title'=> $L['disable'], 'disabled' => false, 'data' => array() );
$breadcrumbPostsData['category'] = array( 'name' => 'category', 'title'=> $L['category'], 'disabled' => false, 'data' => array() );
//$breadcrumbPostsData['tag'] = array( 'name' => 'tag', 'title'=> $L['tag'], 'disabled' => false, 'data' => array() );
$breadcrumbPostsData['blog'] = array( 'name' => 'blog', 'title'=> $L['blog'], 'disabled' => ( $this->adminSettings::IsTrue( 'enable_multiblog', 'site' ) ? false : true ), 'data' => array() );
$breadcrumbPostsData['language'] = array( 'name' => 'language', 'title'=> $L['language'], 'disabled' => ( $this->adminSettings::IsTrue( 'enable_multilang', 'site' ) ? false : true ), 'data' => array() );

//Add values to the "schemaRepresents" array
$schemaRepresents['disable'] = array( 'name' => 'disable', 'title'=> $L['disable'], 'disabled' => false, 'data' => array() );

//Add values to the "contactType" array
$contactType['disable'] = array( 'name' => 'disable', 'title'=> $L['disable'], 'disabled' => false, 'data' => array() );
$contactType['customer-support'] = array( 'name' => 'customer support', 'title'=> $L['customer-support'], 'disabled' => false, 'data' => array() );
$contactType['technical-support'] = array( 'name' => 'technical support', 'title'=> $L['technical-support'], 'disabled' => false, 'data' => array() );
$contactType['billing-support'] = array( 'name' => 'billing support', 'title'=> $L['billing-support'], 'disabled' => false, 'data' => array() );
$contactType['bill-payment'] = array( 'name' => 'bill payment', 'title'=> $L['bill-payment'], 'disabled' => false, 'data' => array() );
$contactType['sales'] = array( 'name' => 'sales', 'title'=> $L['sales'], 'disabled' => false, 'data' => array() );
$contactType['reservations'] = array( 'name' => 'reservations', 'title'=> $L['reservations'], 'disabled' => false, 'data' => array() );
$contactType['credit-card-support'] = array( 'name' => 'credit card support', 'title'=> $L['credit-card-support'], 'disabled' => false, 'data' => array() );
$contactType['emergency'] = array( 'name' => 'emergency', 'title'=> $L['emergency'], 'disabled' => false, 'data' => array() );
$contactType['baggage-tracking'] = array( 'name' => 'baggage tracking', 'title'=> $L['baggage-tracking'], 'disabled' => false, 'data' => array() );
$contactType['roadside-assistance'] = array( 'name' => 'roadside assistance', 'title'=> $L['roadside-assistance'], 'disabled' => false, 'data' => array() );

//Get the pages for this lang
$query = PostsDefaultQuery( "(p.id_site = " . $this->siteID . ") AND (p.id_lang = " . $this->langID . ") AND (p.post_type = 'page') AND (p.post_status = 'published') AND (d.external_url = '' OR d.external_url IS NULL)", null, "p.title ASC" );

$q = $this->db->from( null, $query )->all();

if ( $q )
{
	foreach( $q as $page )
		$pagesData[$page['id_post']] = array( 'name' => $page['id_post'], 'title'=> $page['title'], 'disabled' => false, 'data' => array() );
}

//Create the social network data
//Do it like this, so it will be updated every time we add a new social network in the "socialNetworksArray"
//foreach( $socialNetworksArray as $sId => $sRow )
//{
//	$socialData[$sRow['name']] = array( 'label' => $sRow['title'], 'type' => 'text', 'name' => 'social[' . $sRow['name'] . ']', 'value' => ( isset( $socialMedia[$sRow['name']] ) ? $socialMedia[$sRow['name']] : null ), 'tip' => null );
//}

//Create the form
$form = array
(
	'website-information' => array
	(
		'title' => $L['website-information'],
		'data' => array(
		
			'website-settings' => array( 
				'title' => $L['general'], 'tip' =>$L['schema-generic-info'], 'data' => array
				(
					'this-website-represents'=>array('label'=>$L['this-website-represents'], 'type'=>'select', 'name' => 'schema[site_represents]', 'value'=>( isset( $schema['site_represents'] ) ? $schema['site_represents'] : null ), 'tip'=>null, 'firstNull' => false, 'data' => $schemaRepresents ),
					
					'site-name'=>array( 'label'=>$L['site-name'], 'type'=>'text', 'name' => 'schema[site_name]', 'value' => ( isset( $schema['site_name'] ) ? $schema['site_name'] : null ), 'tip'=>null, 'required'=>( ( !isset( $schema['site_represents'] ) || ( isset( $schema['site_represents'] ) && ( $schema['site_represents'] == 'disable' ) ) ) ? false : true ) ),
					'site-logo'=>array( 'label'=>$L['site-logo'], 'type'=>'text', 'name' => 'schema[site_logo]', 'value' => ( isset( $schema['site_logo'] ) ? $schema['site_logo'] : null ), 'tip'=>$L['site-logo-tip'], 'placeholder' => 'https://' ),
					
					'organization-type'=>array('label'=>$L['organization-type'], 'type'=>'select', 'name' => 'schema[organization_type]', 'value'=>( isset( $schema['organization_type'] ) ? $schema['organization_type'] : null ), 'tip'=>null, 'firstNull' => true, 'data' => $organizationType ),
				)
			),
			
			'breadcrumbs' => array( 
				'title' => $L['breadcrumbs'], 'tip' =>$L['breadcrumbs-info'], 'data' => array
				(
					'enable-breadcrumbs'=>array('label'=>$L['enable-breadcrumbs'], 'type'=>'checkbox', 'name' => 'schema[enable_breadcrumbs]', 'value' => ( isset( $schema['enable_breadcrumbs'] ) ? $schema['enable_breadcrumbs'] : null ), 'tip'=>null ),
					
					'breadcrumb-posts'=>array('label'=>$L['posts'], 'type'=>'select', 'name' => 'schema[breadcrumb_posts]', 'value'=>( isset( $schema['breadcrumb-data']['breadcrumb_posts'] ) ? $schema['breadcrumb-data']['breadcrumb_posts'] : null ), 'tip'=>$L['breadcrumb-add-tip'], 'firstNull' => false, 'data' => $breadcrumbPostsData )
				)
			),
			
			//'social-profiles' => array( 
			//	'title' => $L['social-profiles'], 'tip' =>$L['social-profiles-info'], 'data' => $socialData
			//),

			'contact-information' => array( 
				'title' => $L['contact-information'], 'tip' =>$L['contact-information-info'], 'data' => array
				(
					'contact-type'=>array('label'=>$L['select-contact-type'], 'type'=>'select', 'name' => 'schema[contact_type]', 'value'=>( isset( $schema['contact_type'] ) ? $schema['contact_type'] : null ), 'tip'=>null, 'firstNull' => false, 'data' => $contactType ),
					'contact-page'=>array('label'=>$L['contact-page'], 'name' => 'schema[contact_page]', 'type'=>'select', 'value'=>( $contactPage ? $contactPage['id'] : null ), 'tip'=>null, 'firstNull' => true, 'disabled' => false, 'data' => $pagesData ),
					'contact-number'=>array( 'label'=>$L['contact-number'], 'type'=>'text', 'name' => 'schema[contact_number]', 'value' => ( isset( $schema['contact_number'] ) ? $schema['contact_number'] : null ), 'tip'=>$L['contact-number-tip'], 'placeholder' => 'eg: +1-123-456-7890' ),
				)
			),
		)
	)
);

$code = '<script src="' . HTML_ADMIN_PATH_THEME . 'assets/js/jquery.repeatable.js"></script><script type="text/template" id="add_items">
		<div class="field-group row">
  			<div class="col-sm-10">
				<label for="add_{?}">Item {?}</label>
				<input type="text" class="form-control" name="labels[{?}][task]" value="{task}" id="add_{?}">
  			</div>
			<div class="col-lg-3">
				<input type="button" class="btn btn-danger delete" value="Remove" />
			</div>
  		</div>
		</script>

		<script>
		$(function() {
			$(".card-body .repeatable").repeatable({
				addTrigger: ".card-body .add",
				deleteTrigger: ".card-body .delete",
				template: "#add_items",
				startWith: 1,
				max: 5
			});
		});
		</script>' . PHP_EOL;

$this->AddFooterCode( $code );