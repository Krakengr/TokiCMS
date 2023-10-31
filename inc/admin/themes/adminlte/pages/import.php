<?php
	$settings = $Admin->Settings()::Get();
?>
<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-body p-0">
			<?php if ( !$Admin->IsDefaultSite() || ( $Admin->GetLang() != $Admin->DefaultLang()['id'] ) || ( $Admin->GetBlog() > 0 ) ) : ?>
				<div class="alert alert-warning" role="alert">
					<?php echo __( 'import-browsing-warning' ) ?>
				</div>
			<?php endif ?>
				<div class="bs-stepper">
					<div class="bs-stepper-header" role="tablist">
						<div class="step" data-target="#system-part">
							<button type="button" class="step-trigger" role="tab" aria-controls="system-part" id="system-part-trigger">
								<span class="bs-stepper-circle">1</span>
								<span class="bs-stepper-label"><?php echo __( 'choose-system' ) ?></span>
							</button>
						</div>
						
						<div class="line"></div>
						<div class="step" data-target="#settings-part">
							<button type="button" class="step-trigger" role="tab" aria-controls="settings-part" id="settings-part-trigger">
								<span class="bs-stepper-circle">2</span>
								<span class="bs-stepper-label"><?php echo __( 'settings' ) ?></span>
							</button>
						</div>
						
						<div class="line"></div>
						<div class="step" data-target="#import-part">
							<button type="button" class="step-trigger" role="tab" aria-controls="import-part" id="import-part-trigger">
								<span class="bs-stepper-circle">3</span>
								<span class="bs-stepper-label"><?php echo __( 'import' ) ?></span>
							</button>
						</div>
					</div>
					
					<div class="bs-stepper-content">
						<div id="system-part" class="content" role="tabpanel" aria-labelledby="system-part-trigger">
							<div class="alert alert-info" role="alert">
								<?php echo __( 'import-tip' ) ?>
							</div>
							
							<div class="form-group row">
								<label for="importSystems" class="col-sm-2 col-form-label"><?php echo __( 'system' ) ?></label>
								<div class="col-md-4">
									<select id="importSystems" class="form-control" name="system">
									<?php foreach( $importSystems as $id => $system ) : ?>
										<option value="<?php echo $system['name'] ?>"><?php echo $system['title'] ?></option>
									<?php endforeach ?>
									</select>
								</div>
							</div>
							
							<div class="form-group">
								<button class="btn btn-primary" id="nextPageButton" onclick="stepper.next()" disabled><?php echo __( 'next' ) ?></button>
							</div>
						</div>
						
						<div id="settings-part" class="content" role="tabpanel" aria-labelledby="settings-part-trigger">
							<?php 
								if ( $Admin->MultiBlog() && !empty( $Blogs ) ) :
							?>
							<div class="form-group row">
								<label for="blogSelect" class="col-sm-2 col-form-label"><?php echo __( 'choose-blog' ) ?></label>
								<div class="col-md-4">
									<select id="blogSelect" class="form-control" name="blog">
										<option value="0"><?php echo __( 'choose' ) ?></option>
										<?php
										foreach( $Blogs as $bId => $bData ) : ?>
										<option value="<?php echo $bData['id_blog'] ?>"><?php echo $bData['name'] ?></option>
										<?php endforeach ?>
									</select>
									<small id="blogSelectTip" class="form-text text-muted"><?php echo __( 'import-blog-tip' ) ?></small>
								</div>
							</div>
							<?php endif ?>
							
							<div id="categoryDiv" class="form-group row">
								<label for="category" class="col-sm-2 col-form-label"><?php echo __( 'category' ) ?></label>
								<div class="col-md-4">
									<select id="category" class="form-control" name="category">
									<option value="0">---</option>
									<?php
									if ( $Admin->MultiBlog() ) : //Multiblog has more keys
										foreach( $Categories as $id___ => $row ) : ?>
											<optgroup label="<?php echo $row['name'] ?>">
											<?php if ( !empty( $row['childs'] ) ) : ?>
												<?php foreach( $row['childs'] as $id____ => $childs1 ) : ?>
													<?php if ( !empty( $childs1['childs'] ) ) : ?>
														<?php if ( $childs1['type'] == 'blog' ) : ?>
															<optgroup label="&nbsp;-<?php echo $childs1['name'] ?>">
														<?php endif ?>
														<?php foreach( $childs1['childs'] as $id_____ => $childs2 ) : ?>
															<option value="<?php echo $childs2['id'] ?>">&nbsp;&nbsp;<?php echo ( !empty( $childs2['childs'] ) ? '<strong>' . $childs2['name'] . '</strong>': $childs2['name'] ) ?></option>
														
															<?php if ( !empty( $childs2['childs'] ) ) : ?>
																<?php foreach( $childs2['childs'] as $id______ => $child ) : ?>
																	<option value="<?php echo $child['id'] ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;---<?php echo $child['name'] ?></option>
																<?php endforeach ?>
															<?php endif ?>
														<?php endforeach ?>
													<?php endif ?>

												<?php endforeach ?>
											<?php endif ?>
									
										<?php endforeach ?>
									<?php else : ?>
										<?php foreach( $Categories as $id___ => $row ) : ?>
										<optgroup label="<?php echo $id___ ?>">
										<?php if ( !empty( $row['childs'] ) ) : ?>
											<?php foreach( $row['childs'] as $id____ => $childs1 ) : ?>
												<optgroup label="&nbsp;-<?php echo $childs1['name'] ?>">
												<?php foreach( $childs1['childs'] as $id_____ => $childs2 ) : ?>
													<option value="<?php echo $childs2['id'] ?>">&nbsp;&nbsp;<?php echo ( !empty( $childs2['childs'] ) ? '<strong>' . $childs2['name'] . '</strong>': $childs2['name'] ) ?></option>
													
													<?php if ( !empty( $childs2['childs'] ) ) : ?>
														<?php foreach( $childs2['childs'] as $id______ => $child ) : ?>
															<option value="<?php echo $child['id'] ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;---<?php echo $child['name'] ?></option>
														<?php endforeach ?>
													<?php endif ?>
													
												<?php endforeach ?>
												</optgroup>
											<?php endforeach ?>
										<?php endif ?>
										</optgroup>
										<?php endforeach ?>
									<?php endif ?>
									</select>
									<small id="categoryTip" class="form-text text-muted"><?php echo __( 'import-category-tip' ) ?></small>
								</div>
							</div>
								
							<div class="form-group row">
								<label for="customTypes" class="col-sm-2 col-form-label"><?php echo __( 'custom-post-type' ) ?></label>
								<div class="col-md-4">
									<select id="customTypes" class="form-control" name="custom_post_type">
										<option value="0"><?php echo __( 'default' ) ?></option>
									<?php 
									if ( !empty( $customTypes ) ) : 
										foreach( $customTypes as $id => $custom ) : ?>
										<option value="<?php echo $custom['name'] ?>"><?php echo $custom['title'] ?></option>
									<?php endforeach ?>
									<?php endif ?>
									</select>
								</div>
							</div>
							
							<div class="form-group row">
								<label for="postTypes" class="col-sm-2 col-form-label"><?php echo __( 'post-type' ) ?></label>
								<div class="col-md-4">
									<select id="postTypes" class="form-control" name="post_type">
									<?php foreach( $postTypes as $id => $type ) : ?>
										<option value="<?php echo $type['name'] ?>"><?php echo $type['title'] ?></option>
									<?php endforeach ?>
									</select>
								</div>
							</div>
							
							<div class="form-group row">
								<label for="postStatus" class="col-sm-2 col-form-label"><?php echo __( 'status' ) ?></label>
								<div class="col-md-4">
									<select id="postStatus" class="form-control" name="post_status">
										<option value="published"><?php echo __( 'published' ) ?></option>
										<option value="draft"><?php echo __( 'draft' ) ?></option>
										<option value="scheduled"><?php echo __( 'scheduled' ) ?></option>
										<option value="pending-review"><?php echo __( 'pending-review' ) ?></option>
									</select>
								</div>
							</div>
							
							<div class="form-group row">
								<label for="postAuthor" class="col-sm-2 col-form-label"><?php echo __( 'post-author' ) ?></label>
								<div class="col-md-4">
									<select id="postAuthor" style="width: 100%; height:36px;" name="postAuthor" class="select2"></select>
									<small id="postAuthor" class="form-text text-muted"><?php echo __( 'post-author-tip' ) ?></small>
								</div>
							</div>
							
							<div class="form-group row">
								<label for="oldUrl" class="col-sm-2 col-form-label"><?php echo __( 'your-old-url' ) ?></label>
								<div class="col-md-8">
									<input type="text" name="old_url" class="form-control" id="oldUrl" value="" placeholder="https://">
									<small id="oldUrl" class="form-text text-muted"><?php echo __( 'your-old-url-tip' ) ?></small>
								</div>
							</div>
							
							<div class="form-group row">
								<div class="offset-sm-2 col-md-8">
									<div class="form-check">
										<input type="checkbox" class="form-check-input" name="copy_images" value="1" id="copyImages">
										<label class="form-check-label" for="copyImages"><?php echo __( 'copy-images-locally' ) ?></label>
										<small id="copyImages" class="form-text text-muted"><?php echo __( 'copy-images-locally-tip' ) ?></small>
									</div>
								</div>
							</div>
							
							<hr />
							
							<!-- Content Settings -->
							<h5><?php echo __( 'content-settings' ) ?></h5>
							
							<div class="form-group row">
								<div class="offset-sm-2 col-md-8">
									<div class="form-check">
										<input type="checkbox" class="form-check-input" id="removeHtml">
										<label class="form-check-label" for="removeHtml"><?php echo __( 'remove-html-text-only' ) ?></label>
									</div>
								</div>
							</div>
							
							<div class="form-group row">
								<div class="offset-sm-2 col-md-8">
									<div class="form-check">
										<input type="checkbox" class="form-check-input" id="removeLinks">
										<label class="form-check-label" for="removeLinks"><?php echo __( 'remove-links-from-post-content' ) ?></label>
									</div>
								</div>
							</div>
							
							<div  class="form-group row">
								<label for="remove-old-posts" class="col-sm-2 col-form-label"><?php echo __( 'remove-old-posts' ) ?></label>
								<div class="col-sm-4">
									<input id="remove-old-posts" value="0" type="number" class="form-control border-width-2" name="title_to_tags" step="any" min="0" max="9999" >
									<small id="remove-old-posts-tip" class="form-text text-muted"><?php echo __( 'remove-old-posts-tip' ) ?></small>
								</div>
							</div>
							
							<div class="form-group row">
								<div class="offset-sm-2 col-md-8">
									<div class="form-check">
										<input type="checkbox" class="form-check-input" id="addSourceLink">
										<label class="form-check-label" for="addSourceLink"><?php echo __( 'add-source-link-to-the-content' ) ?></label>
									</div>
								</div>
							</div>
							
							<div class="form-group row">
								<label for="link-text" class="col-sm-2 col-form-label"><?php echo __( 'link-text' ) ?></label>
								<div class="col-sm-10">
									<input type="text" class="form-control" id="link-text" name="linktext" value="" placeholder="e.g. Content retrieved from &quot;source&quot;" >
									<small id="addSouceTextTip" class="form-text text-muted"><?php echo __( 'add-source-text-to-the-content-tip' ) ?></small>
								</div>
							</div>
							
							<div class="form-group row">
								<label for="words-to-avoid" class="col-sm-2 col-form-label"><?php echo __( 'words-to-avoid' ) ?></label>
								<div class="col-sm-10">
									<input type="text" class="form-control" id="words-to-avoid" name="crawl[words_to_avoid]" value="" placeholder="e.g. word1,word2" >
									<small id="wordsToAvoidTip" class="form-text text-muted"><?php echo __( 'words-to-avoid-tip' ) ?></small>
								</div>
							</div>

							<div class="form-group row">
								<div class="offset-sm-2 col-md-8">
									<div class="form-check">
										<input type="checkbox" class="form-check-input" id="autoSetFirstImage">
										<label class="form-check-label" for="autoSetFirstImage"><?php echo __( 'set-the-first-image-in-the-content-as-featured' ) ?></label>
										<small id="autoSetFirstImageTip" class="form-text text-muted"><?php echo __( 'set-the-first-image-in-the-content-as-featured-tip' ) ?></small>
									</div>
								</div>
							</div>
							
							<div class="form-group row">
								<div class="offset-sm-2 col-md-8">
									<div class="form-check">
										<input type="checkbox" class="form-check-input" id="updatePost">
										<label class="form-check-label" for="updatePost"><?php echo __( 'update-post-if-it-is-already-posted' ) ?></label>
										<small id="updatePostTip" class="form-text text-muted"><?php echo __( 'update-post-if-it-is-already-posted-tip' ) ?></small>
									</div>
								</div>
							</div>

							<!-- Custom Settings Div -->
							<div id="customSettingsDiv" class="d-none">
								<hr />

								<!-- Data Cleaner -->
								<h5><?php echo __( 'data-cleaner' ) ?></h5>
								<small class="form-text text-muted"><?php echo __( 'cleans-the-html-of-any-none-html-information' ) ?></small>
								<br />
								
								<div class="form-group row">
									<div class="offset-sm-2 col-md-8">
										<div class="form-check">
											<input type="checkbox" class="form-check-input" id="removeScripts">
											<label class="form-check-label" for="removeScripts"><?php echo __( 'remove-script-tags' ) ?></label>
										</div>
									</div>
								</div>
								
								<div class="form-group row">
									<div class="offset-sm-2 col-md-8">
										<div class="form-check">
											<input type="checkbox" class="form-check-input" id="removeStyles">
											<label class="form-check-label" for="removeStyles"><?php echo __( 'remove-style-tags' ) ?></label>
										</div>
									</div>
								</div>
								
								<div class="form-group row">
									<div class="offset-sm-2 col-md-8">
										<div class="form-check">
											<input type="checkbox" class="form-check-input" id="removeServerSide">
											<label class="form-check-label" for="removeServerSide"><?php echo __( 'remove-server-side-scripts' ) ?></label>
										</div>
									</div>
								</div>
								
								<div class="form-group row">
									<div class="offset-sm-2 col-md-8">
										<div class="form-check">
											<input type="checkbox" class="form-check-input" id="removeSmarty">
											<label class="form-check-label" for="removeSmarty"><?php echo __( 'remove-smarty-scripts' ) ?></label>
										</div>
									</div>
								</div>
								
								<div class="form-group row">
									<div class="offset-sm-2 col-md-8">
										<div class="form-check">
											<input type="checkbox" class="form-check-input" id="removeLineBreaks">
											<label class="form-check-label" for="removeLineBreaks"><?php echo __( 'remove-line-breaks' ) ?></label>
										</div>
									</div>
								</div>
								
								<hr />
								
								<!-- Advanced Crawl Settings -->
								<h5><?php echo __( 'advanced-crawl-settings' ) ?></h5>
								<br />
								
								<div class="form-group row">
									<label for="crawlAsGoogleBot" class="col-sm-2 col-form-label"><?php echo __( 'crawl-as' ) ?></label>
									<div class="col-md-6">
										<select id="crawlAsGoogleBot" class="form-control" name="crawlAsGoogleBot">
											<option value="normal"><?php echo __( 'normal-crawl' ) ?></option>
											<option value="desktop"><?php echo __( 'crawl-as-googlebot-desktop' ) ?></option>
											<option value="mobile"><?php echo __( 'crawl-as-googlebot-mobile' ) ?></option>
										</select>
										<small id="crawlAsGoogleBotTip" class="form-text text-muted"><?php echo __( 'crawl-as-tip' ) ?></small>
									</div>
								</div>
								
								<div class="form-group row">
									<div class="offset-sm-2 col-md-8">
										<div class="form-check">
											<input type="checkbox" class="form-check-input" id="randomIp">
											<label class="form-check-label" for="randomIp"><?php echo __( 'rotate-ip-address-and-user-agent-to-scrape-data' ) ?></label>
											<small id="randomIpTip" class="form-text text-muted"><?php echo __( 'rotate-ip-address-and-user-agent-to-scrape-data-tip' ) ?></small>
										</div>
									</div>
								</div>
								
								<div class="form-group row">
									<label for="num-items" class="col-sm-2 col-form-label"><?php echo __( 'max-posts' ) ?></label>
									<div class="col-sm-10 form-outline">
										<input type="number" step="1" min="1" max="30" class="form-control" id="num-items" value="5" >
										<small id="num-itemsTip" class="form-text text-muted"><?php echo __( 'max-posts-add-tip' ) ?></small>
									</div>
								</div>

								<hr />
								
								<!-- Advanced Content Settings -->
								<h5><?php echo __( 'advanced-content-settings' ) ?></h5>

								<div class="alert alert-info" role="alert"><?php echo __( 'advanced-crawl-settings-tip' ) ?></div>
								
								<br />
						
								<div class="form-group row">
									<label for="post-title" class="col-sm-2 col-form-label"><?php echo __( 'title' ) ?></label>
									<div class="col-sm-10">
										<input type="text" class="form-control" id="post-title" name="regex_title" value="" placeholder="e.g.  &lt;meta property=&quot;og:title&quot; content=&quot;([A-Za-z0-9-]+)&quot; /&gt;" >										
									</div>
								</div>
								
								<div class="form-group row">
									<label for="post-descr" class="col-sm-2 col-form-label"><?php echo __( 'description' ) ?></label>
									<div class="col-sm-10">
										<input type="text" class="form-control" id="post-descr" name="crawl[regex_descr]" value="" placeholder="e.g. &lt;div id=&quot;description&quot;&gt;(.*)&lt;/div&gt;" >
									</div>
								</div>
								
								<div class="form-group row">
									<label for="post-image" class="col-sm-2 col-form-label"><?php echo __( 'featured-image' ) ?></label>
									<div class="col-sm-10">
										<input type="text" class="form-control" id="post-image" name="crawl[regex_image]" value="" placeholder="e.g. &lt;img.+src=['&quot;]([^'&quot;]+)['&quot;].*&gt;" >
									</div>
								</div>
								
								<div class="form-group row">
									<label for="post-content" class="col-sm-2 col-form-label"><?php echo __( 'content' ) ?></label>
									<div class="col-sm-10">
										<input type="text" class="form-control" id="post-content" name="crawl[regex_content]" value="" placeholder="e.g. &lt;div class=&quot;content&quot;&gt;(.*)&lt;/div&gt;" >
									</div>
								</div>
								
								<div class="form-group row">
									<label for="post-tags-container" class="col-sm-2 col-form-label"><?php echo __( 'tags-container' ) ?></label>
									<div class="col-sm-10">
										<input type="text" class="form-control" id="post-tags-container" name="crawl[regex_tags_container]" value="" placeholder="e.g. &lt;div&gt;(.*)&lt;/div&gt;" >
										
										<small id="tagsContainerTip" class="form-text text-muted"><?php echo __( 'tags-container-tip' ) ?></small>
									</div>
								</div>
								
								<div class="form-group row">
									<label for="post-category" class="col-sm-2 col-form-label"><?php echo __( 'category' ) ?></label>
									<div class="col-sm-10">
										<input type="text" class="form-control" id="post-category" name="crawl[regex_cat]" value="" placeholder="e.g. &lt;div class=&quot;category&quot;&gt;(.*)&lt;/div&gt;" >
									</div>
								</div>
								
								<div class="form-group row">
									<label for="post-tags" class="col-sm-2 col-form-label"><?php echo __( 'tags' ) ?></label>
									<div class="col-sm-10">
										<input type="text" class="form-control" id="post-tags" name="crawl[regex_tags]" value="" placeholder="e.g. &lt;div class=&quot;tags&quot;&gt;(.*)&lt;/div&gt;" >
										
										<small id="tagsTip" class="form-text text-muted"><?php echo __( 'tags-preg-tip' ) ?></small>
									</div>
								</div>

								<div class="form-group row">
									<label for="customFields" class="col-sm-2 col-form-label"><?php echo __( 'custom-fields' ) ?></label>
									<div class="col-md-8">
										<div class="table-responsive">
											<table id="customFieldsTable" class="table table-striped table-bordered table-hover">
											<thead>
											<tr>
											<td class="text-left"><?php echo __( 'field' ) ?></td>
											<td class="text-left"><?php echo __( 'value' ) ?></td>
											<td></td>
											</tr>
											</thead>
											<tbody></tbody>
											<tfoot>
											<tr>
											<td colspan="6"></td>
											<td class="text-left"><button type="button" onclick="addCustomField();" data-toggle="tooltip" title="<?php echo __( 'add-custom-field' ) ?>" class="btn btn-primary"><i class="fa fa-plus-circle"></i></button></td>
											</tr>
											</tfoot>
											</table>
										</div>
										<small id="customFields" class="form-text text-muted"><?php echo __( 'import-custom-fields-tip' ) ?></small>
									</div>
								</div>
								
								<div class="form-group row">
									<label for="findReplaceFields" class="col-sm-2 col-form-label"><?php echo __( 'find-and-replace' ) ?></label>
									<div class="col-md-8">
										<div class="table-responsive">
											<table id="findReplaceFieldsTable" class="table table-striped table-bordered table-hover">
											<thead>
											<tr>
											<td class="text-left"><?php echo __( 'find' ) ?></td>
											<td class="text-left"><?php echo __( 'replace' ) ?></td>
											<td></td>
											</tr>
											</thead>
											<tbody></tbody>
											<tfoot>
											<tr>
											<td colspan="6"></td>
											<td class="text-left"><button type="button" onclick="addCustomReplaceField();" data-toggle="tooltip" title="<?php echo __( 'add-new-field' ) ?>" class="btn btn-primary"><i class="fa fa-plus-circle"></i></button></td>
											</tr>
											</tfoot>
											</table>
										</div>
										<small id="findReplaceFieldsTip" class="form-text text-muted"><?php echo __( 'search-replace-fields-tip' ) ?></small>
									</div>
								</div>
							</div>
							<!-- // Custom Settings Div -->

							<div class="form-group">
								<button class="btn btn-primary" onclick="stepper.previous()"><?php echo __( 'previous' ) ?></button>
								
								<button class="btn btn-primary" onclick="stepper.next()"><?php echo __( 'next' ) ?></button>
							</div>
						</div>
						
						<div id="import-part" class="content" role="tabpanel" aria-labelledby="import-part-trigger">
							
							<div class="col-md-12">
								<div id="importBar" class="progress mb-3 d-none">
									<div class="progress-bar bg-success progress-bar-striped" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%"></div>
								</div>
								
								<div id="post-error" class="alert alert-warning alert-dismissible d-none">
									<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
								</div>
								
								<div id="post-success" class="alert alert-info alert-dismissible d-none">
									<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
									<?php echo __( 'import-executed-successfully' ) ?>
								</div>
								<div id="post-status"></div>
							</div>
							
							<div id="importContent" class="col-md-12">
								<div id="uploadXmlDiv" class="d-none">
									<div class="form-group row">
										<label for="enterXmlFileUrl" class="col-sm-2 col-form-label"><?php echo __( 'url' ) ?></label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="enterXmlFileUrl" name="xmlUrl" value="" placeholder="https://www.url-to-file.com/file.xml" >
										</div>
									</div>
								</div>
								
								<div id="uploadDiv" class="d-none">
									<div class="form-group row">
										<label for="fileInput" class="col-sm-2 col-form-label"><?php echo __( 'choose-file' ) ?></label>

										<div class="col-md-8">
											<input id="importXmlInputFile" class="form-control" type="file" name="uploadFile" accept=".zip,.xml">
											<input type="hidden" name="importId" id="importId" value="">
											<small id="importXmlInputFile" class="form-text text-muted"><?php echo sprintf( __( 'max-file-size' ), ini_get('upload_max_filesize') ) ?></small>
										</div>
									
										<div class="col-4 align-items-center">
											<a href="javascript: void(0);" id="upload_button" class="btn btn-primary d-none">
												<i class="fas fa-upload"></i>
												<span><?php echo __( 'start-upload' ) ?></span>
											</a>
											<button type="reset" id="cancel_button" class="btn btn-warning d-none">
												<i class="fas fa-times-circle"></i>
												<span><?php echo __( 'cancel-upload' ) ?></span>
											</button>
										</div>
									</div>
								</div>
								
								<div class="col-4 align-items-center">
									<div id="progressBar" class="progress mb-3 d-none">
										<div class="progress-bar bg-success" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%"></div>
									</div>
								</div>
								
								<input type="hidden" id="siteId" name="siteId" value="<?php echo $Admin->GetSite() ?>">
								<input type="hidden" id="langId" name="langId" value="<?php echo $Admin->GetLang() ?>">

								<div class="form-group">
									<button class="btn btn-primary" id="goBackFromImport" onclick="stepper.previous()"><?php echo __( 'previous' ) ?></button>
								
									<button id="importButton" class="btn btn-success d-none" type="submit"><?php echo __( 'import' ) ?></button>
									
									<button id="resetButton" class="btn btn-warning d-none" type="submit"><?php echo __( 'reset' ) ?></button>
									
									<button id="importButtonXml" class="btn btn-success d-none" type="submit"><?php echo __( 'import' ) ?></button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>