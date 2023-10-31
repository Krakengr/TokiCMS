<?php

$xmlFieldsSelection = array(
	'title' 			=> array( 'name' => __( 'title' ), 				'value' => 'pre::title' ),
	'subtitle' 			=> array( 'name' => __( 'subtitle' ), 			'value' => 'pre::subtitle' ),
	'content' 			=> array( 'name' => __( 'content' ), 			'value' => 'pre::content' ),
	'added' 			=> array( 'name' => __( 'added' ), 				'value' => 'pre::added' ),
	'category' 			=> array( 'name' => __( 'category' ), 			'value' => 'pre::category' ),
	'subcategory' 		=> array( 'name' => __( 'subcategory' ), 		'value' => 'pre::subcategory' ),
	'cover' 			=> array( 'name' => __( 'cover-image' ), 		'value' => 'pre::cover' ),
	'uid' 				=> array( 'name' => __( 'unique-identifier' ),	'value' => 'pre::uid' ),
	'url' 				=> array( 'name' => __( 'url' ),				'value' => 'pre::url' ),
	'manufacturer' 		=> array( 'name' => __( 'manufacturer' ),		'value' => 'pre::manufacturer' ),
	'in-stock' 			=> array( 'name' => __( 'in-stock' ),			'value' => 'pre::in-stock' ),
	'availability' 		=> array( 'name' => __( 'availability' ),		'value' => 'pre::availability' ),
	'updated' 			=> array( 'name' => __( 'updated' ), 			'value' => 'pre::updated' ),
	'current-price' 	=> array( 'name' => __( 'current-price' ), 		'value' => 'pre::current-price' ),
	'discount-price' 	=> array( 'name' => __( 'discount-price' ), 	'value' => 'pre::discount-price' ),
	'rating' 			=> array( 'name' => __( 'rating' ), 			'value' => 'pre::rating' )
);

//Set a few strings from the DB
$sourceExtraData 			= ( $autoSourceData ? Json( $autoSourceData['custom_data'] ) : null );
$xmlData 					= ( $autoSourceData ? Json( $autoSourceData['xml_data'] ) : null );
$sourceSearchReplace 		= ( $sourceExtraData ? $sourceExtraData['search_replace'] : null );
$sourceRegex 				= ( $sourceExtraData ? $sourceExtraData['regex'] : null );
$sourceRegexCustomFields 	= ( $sourceExtraData ? $sourceExtraData['regex']['custom_fields'] : null );
$type 						= $autoSourceData['source_type'];

//XML Selection Fields
$xmlFieldsSelectionHtmlFooter = '<optgroup label="&nbsp;' . __( 'post-settings' ) . '">';

//There are a few predefined keys from the db
foreach( $xmlFieldsSelection as $cus => $cusDt )
{
	$xmlFieldsSelectionHtmlFooter 	.= '<option value="' . $cusDt['value'] . '">' . $cusDt['name'] . '</option>';
}

$xmlFieldsSelectionHtmlFooter .= '</optgroup>';

if ( !empty( $Atts ) )
{
	$xmlFieldsSelectionHtmlFooter .= '<optgroup label="&nbsp;' . __( 'post-attributes' ) . '">';
	
	foreach( $Atts as $att )
	{
		$xmlFieldsSelectionHtmlFooter 	.= '<option value="att::' . $att['id'] . '">' . stripslashes( $att['name'] ) . '</option>';
	}

	$xmlFieldsSelectionHtmlFooter .= '</optgroup>';
}

//Post Buttons template
$postTmpltButtons = array(
	'post-title' => array( 'title' => __( 'title' ), 'var' => '{{title}} ' ),
	'post-description' => array( 'title' => __( 'post-description' ), 'var' => '{{description}} ' ),
	'post-content' => array( 'title' => __( 'content' ), 'var' => '{{content}} ' ),
	'image-url' => array( 'title' => __( 'image-url' ), 'var' => '{{image-url}} ' ),
	'source-url' => array( 'title' => __( 'source-url' ), 'var' => '{{source-url}} ' ),
	'read-more-tag' => array( 'title' => __( 'read-more-tag' ), 'var' => '{{more}} ' )
);

?><div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-body">
				<form class="tab-content" id="form" method="post" action="" autocomplete="off">
					<div class="form-row">
						<div class="form-group col-md-12">
							<h4><?php echo __( 'source-settings' ) ?></h4>

							<div class="form-group row">
								<label for="inputTitle" class="col-sm-3 col-form-label"><?php echo __( 'title' ) ?></label>
								<div class="col-sm-8">
									<input type="text" class="form-control" id="inputTitle" name="title" value="<?php echo htmlspecialchars( $autoSourceData['title'] ) ?>" required />
									<small id="inputTitleTip" class="form-text text-muted"><?php echo __( 'the-title-how-it-appears' ) ?></small>
								</div>
							</div>
							
							<div class="form-group row">
								<label for="inputUrl" class="col-sm-3 col-form-label"><?php echo __( 'source-url' ) ?></label>
								<div class="col-sm-8">
									<input type="text" class="form-control" id="inputUrl" name="url" value="<?php echo $autoSourceData['url'] ?>" placeholder="https://site.com/feed/" required />
									<small id="inputUrlTip" class="form-text text-muted"><?php echo __( 'auto-source-url-tip' ) ?></small>
								</div>
							</div>
							
							<?php if ( $type == 'multi' ) : ?>
							
							<div class="form-group row">
								<label for="inputFeedUrlWrapper" class="col-sm-3 col-form-label"><?php echo __( 'feed-url-wrapper' ) ?></label>
								<div class="col-sm-8">
									<input type="text" class="form-control" id="inputFeedUrlWrapper" name="feed_url_wrapper" value="<?php echo $sourceRegex['feed_url_wrapper'] ?>" placeholder="<div class=&quot;source&quot;><a href=&quot;(.*?)&quot;>Source</a></div>" />
									<small id="inputFeedUrlWrapperTip" class="form-text text-muted"><?php echo __( 'feed-url-wrapper-tip' ) ?></small>
								</div>
							</div>

							<?php else : ?>
							
							<?php if ( $type == 'xml' ) : ?>
							
							<hr />
							
							<h4><?php echo __( 'xml-settings' ) ?></h4>
							
							<div class="form-group row">
								<label for="xmlFileType" class="col-sm-2 col-form-label"><?php echo __( 'xml-file-type' ) ?></label>
								<div class="col-sm-6">
									<select id="xmlFileType" class="form-control" name="xml_type">
										<option value="sitemap" <?php echo ( ( !empty( $xmlData['file_type'] ) && ( $xmlData['file_type'] == 'sitemap' ) ) ? 'selected' : '' ) ?>><?php echo __( 'sitemap-file' ) ?></option>
										<option value="feed" <?php echo ( ( !empty( $xmlData['file_type'] ) && ( $xmlData['file_type'] == 'feed' ) ) ? 'selected' : '' ) ?>><?php echo __( 'xml-product-feed' ) ?></option>
										<option value="index" <?php echo ( ( !empty( $xmlData['file_type'] ) && ( $xmlData['file_type'] == 'index' ) ) ? 'selected' : '' ) ?>><?php echo __( 'sitemap-index-file' ) ?></option>
									</select>
									<small id="xmlFileTypeTip" class="form-text text-muted"><?php echo __( 'xml-file-type-tip' ) ?></small>
								</div>
							</div>
							
							<div class="form-group row">
								<label for="copyXmlFile" class="col-sm-3 col-form-label"><?php echo __( 'copy-xml-file-locally' ) ?></label>
								<div class="col-sm-8">
									<input type="number" class="form-control" id="copyXmlFile" name="copy_xml_locally" value="<?php echo ( !empty( $xmlData['copy_xml_locally'] ) ? $xmlData['copy_xml_locally'] : 0 ) ?>" min="0" />
									<small id="copyXmlFileTip" class="form-text text-muted"><?php echo __( 'copy-xml-file-locally-tip' ) ?></small>
								</div>
							</div>
							
							<?php if ( $xmlData['file_type'] == 'feed' ) : ?>
							<div id="storeSelection" class="form-group row">
								<label for="storeSelection" class="col-sm-2 col-form-label"><?php echo __( 'choose-store' ) ?></label>
								<div class="col-md-4">
									<select id="storeSelect" style="width: 100%; height:36px;" name="xml_store" class="select2"></select>
									<small id="storeSelectTip" class="form-text text-muted"><?php echo __( 'import-choose-store-tip' ) ?></small>
								</div>
							</div>
							
							<div class="form-group row">
								<label for="xmlItemsWrapper" class="col-sm-3 col-form-label"><?php echo __( 'items-wrapper' ) ?></label>
								<div class="col-sm-8">
									<input type="text" class="form-control" id="xmlItemsWrapper" name="xml_items_wrapper" value="<?php echo $xmlData['items_wrapper'] ?>" placeholder="channel" />
									<small id="xmlItemsWrapperTip" class="form-text text-muted"><?php echo __( 'xml-items-wrapper-tip' ) ?></small>
								</div>
							</div>
							
							<div class="form-group row">
								<label for="xmlItemWrapper" class="col-sm-3 col-form-label"><?php echo __( 'single-item-wrapper' ) ?></label>
								<div class="col-sm-8">
									<input type="text" class="form-control" id="xmlItemWrapper" name="xml_item_wrapper" value="<?php echo $xmlData['item_wrapper'] ?>" placeholder="item" />
									<small id="xmlItemWrapperTip" class="form-text text-muted"><?php echo __( 'xml-single-item-wrapper-tip' ) ?></small>
								</div>
							</div>

							<div id="customXmlFields" class="form-group row">
								<label for="customXmlFieldsTable" class="col-sm-2 col-form-label"><?php echo __( 'data-fields' ) ?></label>
								<div class="col-md-8">
									<div class="table-responsive">
									<table id="customXmlFieldsTable" class="table table-striped table-bordered table-hover">
									<thead>
									<tr>
									<td class="text-left"><?php echo __( 'attribute' ) ?></td>
									<td class="text-left"><?php echo __( 'value' ) ?></td>
									<td></td>
									</tr>
									</thead>
									<tbody>
									<?php 
									if ( !empty( $xmlData['values'] ) )
									{
										foreach( $xmlData['values'] as $customXmlFieldsID => $customXmlFieldsValue )
										{
											echo '<tr id="xmlElement-row' . $customXmlFieldsID . '">';
											
											echo '<td class="text-right"><input type="text" name="xml_feed_values[' . $sourceSearchReplaceID . '][search]" value="' . $customXmlFieldsValue['attribute'] . '" placeholder="' . __( 'search' ) . '" class="form-control" /></td>';
											
											echo '<td class="text-right"><select id="customFieldSelect" class="form-control shadow-none" style="width: 100%; height:36px;" name="xml_feed_values[' . $sourceSearchReplaceID . '][value]" aria-label="Custom Fields">';
											
											echo '<optgroup label="&nbsp;' . __( 'post-settings' ) . '">';
											
											foreach( $xmlFieldsSelection as $cus => $cusDt )
											{
												echo '<option value="' . $cusDt['value'] . '">' . $cusDt['name'] . '</option>';
											}

											echo '</optgroup>';

											if ( !empty( $Atts ) )
											{
												echo '<optgroup label="&nbsp;' . __( 'post-attributes' ) . '">';

												foreach( $Atts as $att )
												{
													echo '<option value="att::' . $att['id'] . '">' . stripslashes( $att['name'] ) . '</option>';
												}

												echo '</optgroup>';
											}

											echo '</select></td>';
											
											echo '<td class="text-left"><button type="button" onclick="$(\'#xmlElement-row' . $sourceSearchReplaceID . '\').remove();" data-toggle="tooltip" title="' . __( 'remove' ) . '" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>';
											
											echo '</tr>';
										}
									}
									?>
									</tbody>
									<tfoot>
									<tr>
									<td colspan="6"></td>
									<td class="text-left"><button type="button" onclick="addCustomXMLField();" data-toggle="tooltip" title="<?php echo __( 'add-field' ) ?>" class="btn btn-primary"><i class="fa fa-plus-circle"></i></button></td>
									</tr>
									</tfoot>
									</table>
									</div>
									<small id="customXmlFieldsTableTip" class="form-text text-muted"><?php echo __( 'xml-data-fields-tip' ) ?></small>
								</div>
							</div>	
							
							<?php 
								//XML file type
								endif; 
							?>
							
							<?php 
								//XML type
								endif; 
							?>
							
							<?php 
								//else
								endif; 
							?>
							
							<?php if ( ( $type != 'xml' ) || ( ( $type == 'xml' ) && ( $xmlData['file_type'] != 'feed' ) ) ) : ?>

							<div class="form-group row">
								<label for="inputAvoidWords" class="col-sm-3 col-form-label"><?php echo __( 'words-to-avoid' ) ?></label>
								<div class="col-sm-8">
									<input type="text" class="form-control" id="inputAvoidWords" name="avoid_words" value="<?php echo $autoSourceData['avoid_words'] ?>" placeholder="word1,word2,word3" />
									<small id="inputAvoidWordsTip" class="form-text text-muted"><?php echo __( 'words-to-avoid-tip' ) ?></small>
								</div>
							</div>
							
							<div class="form-group row">
								<label for="inputReqWords" class="col-sm-3 col-form-label"><?php echo __( 'required-words' ) ?></label>
								<div class="col-sm-8">
									<input type="text" class="form-control" id="inputReqWords" name="required_words" value="<?php echo $autoSourceData['required_words'] ?>" placeholder="word1,word2,word3" />
									<small id="inputReqWordsTip" class="form-text text-muted"><?php echo __( 'required-words-tip' ) ?></small>
								</div>
							</div>
							
							<div class="form-group row">
								<label for="titleToTags" class="col-sm-3 col-form-label"><?php echo __( 'source-title-keywords-to-tags' ) ?></label>
								<div class="col-sm-8">
									<input type="number" class="form-control" id="titleToTags" name="title_to_tags" value="<?php echo $autoSourceData['add_tags'] ?>" min="0" max="5" />
									<small id="titleToTagsTip" class="form-text text-muted"><?php echo __( 'source-title-keywords-to-tags-tip' ) ?></small>
								</div>
							</div>
							
							<div class="form-group row">
								<label for="postAutoDeletion" class="col-sm-3 col-form-label"><?php echo __( 'enable-post-auto-deletion-feature-days' ) ?></label>
								<div class="col-sm-6">
									<input type="number" class="form-control" id="postAutoDeletion" name="auto_deletion" value="<?php echo $autoSourceData['auto_delete_days'] ?>" min="0" max="365" />
									<small id="postAutoDeletionTip" class="form-text text-muted"><?php echo __( 'enable-post-auto-deletion-feature-days-tip' ) ?></small>
								</div>
							</div>
							
							<div class="form-group row">
								<label for="maxPosts" class="col-sm-3 col-form-label"><?php echo __( 'max-posts' ) ?></label>
								<div class="col-sm-6">
									<input type="number" class="form-control" id="maxPosts" name="max_posts" value="<?php echo $autoSourceData['max_posts'] ?>" min="0" max="100" />
									<small id="maxPostsTip" class="form-text text-muted"><?php echo __( 'max-posts-tip' ) ?></small>
								</div>
							</div>
							
							<div class="form-group row">
								<label for="skipPosts" class="col-sm-3 col-form-label"><?php echo __( 'skip-posts-older-than-days' ) ?></label>
								<div class="col-sm-6">
									<input type="number" class="form-control" id="skipPosts" name="skip_older_posts" value="<?php echo $autoSourceData['skip_posts_days'] ?>" min="0" max="100" />
									<small id="skipPostsTip" class="form-text text-muted"><?php echo __( 'skip-posts-older-than-days-tip' ) ?></small>
								</div>
							</div>
							
							<div class="form-check">
								<input class="form-check-input" type="checkbox" value="1" name="copy_images" id="copyImagesLocally" <?php echo ( IsTrue( $autoSourceData['copy_images'] ) ? 'checked' : '' ) ?>>
								<label class="form-check-label" for="copyImagesLocally">
									<?php echo __( 'copy-images-from-content-locally' ) ?>
								</label>
								<small id="copyImagesLocallyHelp" class="form-text text-muted"><?php echo __( 'copy-images-from-content-locally-tip' ) ?></small>
							</div>
							
							<div class="form-check">
								<input class="form-check-input" type="checkbox" value="1" name="skip_posts_without_images" id="skipNoImages" <?php echo ( IsTrue( $autoSourceData['skip_posts_no_images'] ) ? 'checked' : '' ) ?>>
								<label class="form-check-label" for="skipNoImages">
									<?php echo __( 'skip-posts-that-dont-have-images' ) ?>
								</label>
								<small id="skipNoImagesHelp" class="form-text text-muted"><?php echo __( 'skip-posts-that-dont-have-images-tip' ) ?></small>
							</div>
							
							<div class="form-check">
								<input class="form-check-input" type="checkbox" value="1" name="strip_html" id="stripHtmlFromContent" <?php echo ( IsTrue( $autoSourceData['strip_html'] ) ? 'checked' : '' ) ?>>
								<label class="form-check-label" for="stripHtmlFromContent">
									<?php echo __( 'strip-html-from-content' ) ?>
								</label>
								<small id="stripHtmlFromContentHelp" class="form-text text-muted"><?php echo __( 'strip-html-from-content-tip' ) ?></small>
							</div>
							
							<div class="form-check">
								<input class="form-check-input" type="checkbox" value="1" name="strip_links" id="stripLinksFromContent" <?php echo ( IsTrue( $autoSourceData['strip_links'] ) ? 'checked' : '' ) ?>>
								<label class="form-check-label" for="stripLinksFromContent">
									<?php echo __( 'strip-links-from-the-post' ) ?>
								</label>
								<small id="stripLinksFromContentHelp" class="form-text text-muted"><?php echo __( 'strip-links-from-the-post-tip' ) ?></small>
							</div>
							
							<div class="form-check">
								<input class="form-check-input" type="checkbox" value="1" name="remove_images" id="removeImages" <?php echo ( IsTrue( $autoSourceData['remove_images'] ) ? 'checked' : '' ) ?>>
								<label class="form-check-label" for="removeImages">
									<?php echo __( 'remove-images-from-content' ) ?>
								</label>
							</div>
							
							<div class="form-check">
								<input class="form-check-input" type="checkbox" value="1" name="set_cover" id="setCoverImage" <?php echo ( IsTrue( $autoSourceData['set_first_image_cover'] ) ? 'checked' : '' ) ?>>
								<label class="form-check-label" for="setCoverImage">
									<?php echo __( 'set-the-first-image-as-cover-image' ) ?>
								</label>
							</div>
							
							<div class="form-check">
								<input class="form-check-input" type="checkbox" value="1" name="set_original_date" id="setOriginalDate" <?php echo ( IsTrue( $autoSourceData['set_original_date'] ) ? 'checked' : '' ) ?>>
								<label class="form-check-label" for="setOriginalDate">
									<?php echo __( 'use-original-date-if-possible' ) ?>
								</label>
							</div>
							
							<div class="form-check">
								<input class="form-check-input" type="checkbox" value="1" name="set_source" id="setSourceLink" <?php echo ( IsTrue( $autoSourceData['set_source_link'] ) ? 'checked' : '' ) ?>>
								<label class="form-check-label" for="setSourceLink">
									<?php echo __( 'set-source-link-at-the-end-of-the-post' ) ?>
								</label>
							</div>
							<br />
							
							<div id="searchReplace" class="form-group row">
								<label for="searchReplaceFields" class="col-sm-2 col-form-label"><?php echo __( 'search-and-replace' ) ?></label>
								<div class="col-md-8">
									<div class="table-responsive">
									<table id="searchReplaceFieldsTable" class="table table-striped table-bordered table-hover">
									<thead>
									<tr>
									<td class="text-left"><?php echo __( 'search' ) ?></td>
									<td class="text-left"><?php echo __( 'replace' ) ?></td>
									<td></td>
									</tr>
									</thead>
									<tbody>
									<?php 
									if ( !empty( $sourceSearchReplace ) )
									{
										foreach( $sourceSearchReplace as $sourceSearchReplaceID => $sourceSearchReplaceSinge )
										{
											echo '<tr id="searchReplace-row' . $sourceSearchReplaceID . '">';
											
											echo '<td class="text-right"><input type="text" name="search_replace[' . $sourceSearchReplaceID . '][search]" value="' . $sourceSearchReplaceSinge['search'] . '" placeholder="' . __( 'search' ) . '" class="form-control" /></td>';
											
											echo '<td class="text-right"><input type="text" name="search_replace[' . $sourceSearchReplaceID . '][replace]" value="' . $sourceSearchReplaceSinge['replace'] . '" placeholder="' . __( 'replace' ) . '" class="form-control" /></td>';
											
											echo '<td class="text-left"><button type="button" onclick="$(\'#searchReplace-row' . $sourceSearchReplaceID . '\').remove();" data-toggle="tooltip" title="' . __( 'remove' ) . '" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>';
											
											echo '</tr>';
										}
									}
									?>
									</tbody>
									<tfoot>
									<tr>
									<td colspan="6"></td>
									<td class="text-left"><button type="button" onclick="addSearchReplaceField();" data-toggle="tooltip" title="<?php echo __( 'add-field' ) ?>" class="btn btn-primary"><i class="fa fa-plus-circle"></i></button></td>
									</tr>
									</tfoot>
									</table>
									</div>
									<small id="addSearchReplaceField" class="form-text text-muted"><?php echo __( 'search-replace-fields-tip' ) ?></small>
								</div>
						</div>	
							
							<hr />
							
							<h4><?php echo __( 'post-settings' ) ?></h4>
							
							<div class="form-group row">
								<label for="postType" class="col-sm-2 col-form-label"><?php echo __( 'post-type' ) ?></label>
								<div class="col-sm-6">
									<select id="postType" class="form-control" name="post_type">
										<option value="post" <?php echo ( ( $autoSourceData['post_type'] == 'post' ) ? 'selected' : '' ) ?>><?php echo __( 'post' ) ?></option>
										<option value="page" <?php echo ( ( $autoSourceData['post_type'] == 'page' ) ? 'selected' : '' ) ?>><?php echo __( 'page' ) ?></option>
									</select>
									<small id="postTypeTip" class="form-text text-muted"><?php echo __( 'post-type-tip' ) ?></small>
								</div>
							</div>
							
							<div class="form-group row">
								<label for="postStatus" class="col-sm-2 col-form-label"><?php echo __( 'status' ) ?></label>
								<div class="col-sm-6">
									<select id="postStatus" class="form-control" name="post_status">
										<option value="published" <?php echo ( ( $autoSourceData['post_status'] == 'published' ) ? 'selected' : '' ) ?>><?php echo __( 'published' ) ?></option>
										<option value="draft" <?php echo ( ( $autoSourceData['post_status'] == 'draft' ) ? 'selected' : '' ) ?>><?php echo __( 'draft' ) ?></option>
									</select>
									<small id="postStatusTip" class="form-text text-muted"><?php echo __( 'post-status-tip' ) ?></small>
								</div>
							</div>
							
							<div class="form-group row">
								<label for="postStatus" class="col-sm-2 col-form-label"><?php echo __( 'category' ) ?></label>
								<div class="col-sm-6">
									<select id="postStatus" class="form-control" name="category">
										<option value="published" <?php echo ( ( $autoSourceData['post_status'] == 'published' ) ? 'selected' : '' ) ?>><?php echo __( 'published' ) ?></option>
										<option value="draft" <?php echo ( ( $autoSourceData['post_status'] == 'draft' ) ? 'selected' : '' ) ?>><?php echo __( 'draft' ) ?></option>
									</select>
									<small id="postStatusTip" class="form-text text-muted"><?php echo __( 'source-category-tip' ) ?></small>
								</div>
							</div>
							
							<div class="form-group row">
								<label for="postCategory" class="col-sm-2 col-form-label"><?php echo __( 'category' ) ?></label>
								<div class="col-sm-6">
									<select id="postCategory" class="form-control" name="category">
										<option value="0"><?php echo __( 'choose' ) ?>...</option>
										<?php foreach ( $Categories as $key => $cat ) :
										if ( !empty( $cat['childs'] ) ) : ?>
										<optgroup label="<?php echo $cat['name'] ?>">
										<?php foreach( $cat['childs'] as $_ => $child ) :
											if ( !empty( $child['childs'] ) ) : ?>
											<optgroup label="&nbsp;<?php echo $child['name'] ?>">
											<?php foreach( $child['childs'] as $__ => $ch ) : ?>
												<option value="<?php echo $ch['id'] ?>" <?php echo ( ( $autoSourceData['id_category'] == $ch['id'] ) ? 'selected' : '' ) ?>><?php echo $ch['name'] ?></option>
												<?php if ( !empty( $ch['childs'] ) ) :
													foreach( $ch['childs'] as $___ => $chi ) : ?>
													<option value="<?php echo $chi['id'] ?>" <?php echo ( ( $autoSourceData['id_category'] == $chi['id'] ) ? 'selected' : '' ) ?>>¦&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $chi['name'] ?></option>
													<?php endforeach ?>
												<?php endif ?>
											<?php endforeach ?>
											</optgroup>
										<?php endif ?>
										<?php endforeach ?>
										</optgroup>
										<?php endif ?>
										<?php endforeach ?>
									</select>
									<small id="postCategoryTip" class="form-text text-muted"><?php echo __( 'source-category-tip' ) ?></small>
								</div>
							</div>
							
							<div class="form-group row">
								<label for="postAutoCategory" class="col-sm-2 col-form-label"><?php echo __( 'auto-add-categories' ) ?></label>
								<div class="col-sm-6">
									<select id="postAutoCategory" class="form-control" name="auto_category">
										<option value="0"><?php echo __( 'choose' ) ?>...</option>
										<?php 
										foreach( $Categories as $cc => $cd )
										{
											if ( $cd['type'] == 'lang' )
											{
												echo '<option value="lang::' . $cd['id'] . '">' . $cd['name'] . '</option>';
												
												if ( !empty( $cd['childs'] ) )
												{
													foreach( $cd['childs'] as $chd )
													{
														if ( ( $chd['type'] == 'blog' ) && ( $chd['id'] > 0 ) )
														{
															echo '<option value="blog::' . $chd['id'] . '">¦&nbsp;&nbsp;&nbsp;&nbsp;' . $chd['name'] . '</option>';
														}
													}
												}
											}
										}
										?>
									</select>
									<small id="postAutoCategoryTip" class="form-text text-muted"><?php echo __( 'auto-add-categories-tip' ) ?></small>
								</div>
							</div>
							
							<div class="form-group row">
								<label for="postAuthor" class="col-sm-2 col-form-label"><?php echo __( 'post-author' ) ?></label>
								<div class="col-md-4">
									<select id="postAuthor" style="width: 100%; height:36px;" name="post_author" class="select2">
										<option value="<?php echo $autoSourceData['user_id'] ?>"><?php echo ( !empty( $autoSourceData['real_name'] ) ? $autoSourceData['real_name'] : $autoSourceData['user_name'] ) ?></option>
									</select>
									<small id="postAuthor" class="form-text text-muted"><?php echo __( 'post-author-tip' ) ?></small>
								</div>
							</div>
							
							<?php if ( $type != 'xml' ) : ?>
							<div id="storeSelection" class="form-group row">
								<label for="storeSelection" class="col-sm-2 col-form-label"><?php echo __( 'choose-store' ) ?></label>
								<div class="col-md-4">
									<select id="storeSelect" style="width: 100%; height:36px;" name="post_store" class="select2"></select>
									<small id="storeSelectTip" class="form-text text-muted"><?php echo __( 'import-choose-store-tip' ) ?></small>
								</div>
							</div>			
							<?php endif ?>
							
							<div class="form-group row">
								<label for="post-settings" class="col-sm-2 col-form-label"><?php echo __( 'post-template' ) ?></label>
								<div class="col-sm-10">
									<textarea class="form-control" id="post-template" name="post_template" rows="3" ></textarea>
									
									<?php foreach( $postTmpltButtons as $bId => $bRow ) : ?>
									<button type="button" class="btn btn-secondary btn-sm" id="<?php echo $bId ?>" data-value="<?php echo $bRow['var'] ?>"><?php echo $bRow['title'] ?></button>
									<script type="text/javascript">
									$('button[id^="<?php echo $bId ?>"]').on('click', function() {
										var $target 	= $('#post-template'),
											text 		= $('#post-template').val(),
											buttonVal 	= $(this).data('value');
										$target.val(`${text}${buttonVal}`);
									});</script>
									<?php endforeach ?>
									<small id="post-template-tip" class="form-text text-muted"><?php echo __( 'post-template-tip' ) ?></small>
								</div>
							</div>
							
							<hr />
							
							<h4><?php echo __( 'advanced-settings' ) ?></h4>
							
							<div class="alert alert-info" role="alert"><?php echo __( 'advanced-crawl-settings-tip' ) ?></div>
							
							<div class="form-group row">
								<label for="crawlAs" class="col-sm-2 col-form-label"><?php echo __( 'crawl-as' ) ?></label>
								<div class="col-md-4">
									<select id="crawlAs" style="width: 100%; height:36px;" name="crawl_as" class="form-control">
										<option value="normal" <?php echo ( ( $sourceRegex['crawl_as'] == 'normal' ) ? 'selected' : '' ) ?>><?php echo __( 'normal-crawl' ) ?></option>
										<option value="desktop" <?php echo ( ( $sourceRegex['crawl_as'] == 'desktop' ) ? 'selected' : '' ) ?>><?php echo __( 'crawl-as-googlebot-desktop' ) ?></option>
										<option value="mobile" <?php echo ( ( $sourceRegex['crawl_as'] == 'mobile' ) ? 'selected' : '' ) ?>><?php echo __( 'crawl-as-googlebot-mobile' ) ?></option>
									</select>
									<small id="crawlAsTip" class="form-text text-muted"><?php echo __( 'crawl-as-tip' ) ?></small>
								</div>
							</div>
							
							<div class="form-check">
								<input class="form-check-input" type="checkbox" value="1" name="rotate_ip_address" id="rotateIpAddress" <?php echo ( IsTrue( $sourceRegex['rotate_ip_address'] ) ? 'checked' : '' ) ?>>
								<label class="form-check-label" for="rotateIpAddress">
									<?php echo __( 'rotate-ip-address-and-user-agent-to-scrape-data' ) ?>
								</label>
								<small id="rotateIpAddressTip" class="form-text text-muted"><?php echo __( 'rotate-ip-address-and-user-agent-to-scrape-data-tip' ) ?></small>
							</div>

							<?php endif ?>
							<hr />

							<div class="form-check">
								<input class="form-check-input" type="checkbox" value="1" name="delete" id="deleteCheckBox" >
								<label class="form-check-label" for="deleteCheckBox">
									<?php echo __( 'delete-source' ) ?>
								</label>
								<small id="deleteCheckBoxHelp" class="form-text text-muted"><?php echo __( 'delete-content-source-tip' ) ?></small>
							</div>
							
							<br />
							<div class="form-check">
								<input class="form-check-input" type="checkbox" value="1" name="delete_content" id="deleteCheckBox" >
								<label class="form-check-label" for="deleteCheckBox">
									<?php echo __( 'delete-content' ) ?>
								</label>
								<small id="deleteCheckBox2Help" class="form-text text-muted"><?php echo __( 'delete-source-content-tip' ) ?></small>
							</div>
							
							<br />
							<div class="align-middle">
								<div class="float-left mt-1">
									<button type="submit" class="btn btn-primary btn-sm" name="save"><?php echo __( 'save' ) ?></button>
									<a class="btn btn-secondary btn-sm" href="<?php echo $Admin->GetUrl( 'auto-content-sources' ) ?>" role="button"><?php echo __( 'cancel' ) ?></a>
								</div>
							</div>
						</div>
					</div>
					<input type="hidden" name="_token" value="<?php echo generate_token( 'editSource' . $autoSourceData['id'] ) ?>">
				</form>
			</div>
		</div>
	</div>
</div>