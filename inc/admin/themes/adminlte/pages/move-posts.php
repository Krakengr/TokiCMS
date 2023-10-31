<?php

$hasLangs = $hasBlogs = $hasSites = false;

$fromArray = array(
	'orphan-posts' 	=> array( 'title' => __( 'orphan-posts-move' ) ),
	'orphan-pages' 	=> array( 'title' => __( 'orphan-pages-move' ) ),
	'orphan' 		=> array( 'title' => __( 'orphan-content-move' ) ),
	'category'		=> array( 'title' => __( 'categories-posts-move' ) ),
);

$targetArrayOrphan = array(
	'orphan-posts' 	=> array( 'title' => __( 'orphan-content-target' ) )
);

//Add the Blogs in Array
if ( $Admin->Settings()::IsTrue( 'enable_multiblog', 'site' ) )
{
	$fromArray['blog'] = array( 'title' => __( 'blog-content-move' ) );
	
	$targetArrayNoOrphan['blog'] = array( 'title' => __( 'blog-content-target' ) );

	$hasBlogs = true;
	
	$Blogs = $Admin->GetBlogs();
}

//Add the Langs into Array
if ( $Admin->Settings()::IsTrue( 'enable_multilang', 'site' ) )
{
	$fromArray['lang'] = array( 'title' => __( 'lang-content-move' ) );
	
	$targetArrayNoOrphan['lang'] = array( 'title' => __( 'language-content-target' ) );
	
	$hasLangs = true;
	
	$Langs = $Admin->OtherLangs(); 
}

//Add the Sites in the Array
if ( MULTISITE )
{
	$fromArray['site'] = array( 'title' => __( 'site-content-move' ) );
	
	$targetArrayNoOrphan['site'] = array( 'title' => __( 'site-content-target' ) );
	
	$hasSites = true;
	
	$Sites = $Admin->Sites();
}

$targetArrayOrphan += $targetArrayNoOrphan;

$cats = GetAdminCategories( 'name', 'ASC', true, false );

?>
<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-body">
				<div class="form-row">
					<div class="form-group col-md-9">
						<h4><?php echo __( 'move-posts' ) ?></h4>
						
						<div class="form-group">
							<label for="inputFrom"><?php echo __( 'from' ) ?></label>
							<select class="form-control" id="inputFrom" >
								<option value=""><?php echo __( 'select' ) ?>...</option>
							<?php foreach( $fromArray as $f => $t ) : ?>
								<option value="<?php echo $f ?>"><?php echo $t['title'] ?></option>
							<?php endforeach ?>
							</select>
							<small id="inputFromHelp" class="form-text text-muted"><?php echo __( 'move-posts-tip' ) ?></small>
						</div>

						<div id="targetOptionsNoOrphan" class="mb-3 d-none">
							<div class="form-group">
								<label for="inputTarget"><?php echo __( 'target' ) ?></label>
								<select class="form-control" id="inputTarget" >
									<option value=""><?php echo __( 'select' ) ?>...</option>
								<?php foreach( $targetArrayNoOrphan as $f => $t ) : ?>
									<option value="<?php echo $f ?>"><?php echo $t['title'] ?></option>
								<?php endforeach ?>
								</select>
							</div>

						</div>

						<div id="targetOrphanCategoryDiv" class="mb-3 d-none">
							<div class="form-group">
								<label for="toCategoryOrphan"><?php echo __( 'choose-category' ) ?></label>
								<select class="form-control" id="toCategoryOrphan" >
								<?php if ( !empty( $cats ) ) :
									foreach( $cats as $cat ) : ?>
										<option value="<?php echo $cat['id'] ?>"><?php echo $cat['name'] ?><?php echo ( ( isset( $cat['isDefault'] ) && $cat['isDefault'] ) ? ' [' . __( 'default' ) . ']' : '' ) ?></option>
									<?php endforeach ?>
								<?php endif ?>
								</select>
							</div>
						</div>
						
						<div id="targetOrphanBlogDiv" class="mb-3 d-none">
							<div class="form-group">
								<label for="toBlogOrphan"><?php echo __( 'choose-blog' ) ?></label>
								<select class="form-control" id="toBlogOrphan" >
									<option value=""><?php echo __( 'select' ) ?>...</option>
									<?php if ( !empty( $Blogs ) ) :
										foreach( $Blogs as $bId => $bData ) : ?>
									<option value="<?php echo $bData['id_blog'] ?>"><?php echo $bData['name'] ?></option>
										<?php endforeach ?>
									<?php endif ?>
								</select>
							</div>
						</div>
						
						<div id="targetOrphanLangDiv" class="mb-3 d-none">
							<div class="form-group">
								<label for="toLangOrphan"><?php echo __( 'choose-language' ) ?></label>
								<select class="form-control" id="toLangOrphan" >
									<option value=""><?php echo __( 'select' ) ?>...</option>
									
									<?php if ( $Admin->GetLang() != $Admin->DefaultLang()['id'] ) : ?>
									<option value="<?php echo $Admin->DefaultLang()['id'] ?>"><?php echo $Admin->DefaultLang()['title'] ?></option>
									<?php endif ?>
									
									<?php if ( !empty( $Langs ) ) :
										foreach( $Langs as $lId => $lData ) : 
											
											if ( $lId == $Admin->GetLang() )
												continue;
										?>
										<option value="<?php echo $lId ?>"><?php echo $lData['lang']['title'] ?></option>
										<?php endforeach ?>
									<?php endif ?>
								</select>
							</div>
						</div>
						
						<div id="targetOrphanSiteDiv" class="mb-3 d-none">
							<div class="form-group">
								<label for="toSiteOrphan"><?php echo __( 'choose-site' ) ?></label>
								<select class="form-control" id="toSiteOrphan" >
									<option value=""><?php echo __( 'select' ) ?>...</option>
									<?php if ( SITE_ID != $Admin->GetSite() ) : ?>
									<option value="<?php echo SITE_ID ?>"><?php echo $Admin->DefaultSiteName() ?></option>
									<?php endif ?>
									
									<?php if ( !empty( $Sites ) ) :
										foreach( $Sites as $singeSite ) :
										
											if ( $singeSite['id'] == $Admin->GetSite() )
												continue;
										?>
									<option value="<?php echo $singeSite['id'] ?>"><?php echo $singeSite['title'] ?></option>
										<?php endforeach ?>
									<?php endif ?>
								</select>
							</div>
						</div>

						<div id="loaderShow" class="form-group d-none">
							<label>&nbsp;</label>
							<img class="form-check" src="<?php echo HTML_ADMIN_PATH_THEME ?>assets/img/ajax-loader.gif">
						</div> 
						
						<div id="targetCategoryDiv" class="row mb-3 d-none">
							<div class="col-sm-6">
								<div class="form-group">
									<label><?php echo __( 'choose-category' ) ?></label>
									<select id="fromCategory" class="form-control">
										<option value=""><?php echo __( 'select' ) ?>...</option>
										<?php if ( !empty( $cats ) ) :
											foreach( $cats as $cat ) : ?>
											<option value="<?php echo $cat['id'] ?>"><?php echo $cat['name'] ?><?php echo ' [' . ( !empty( $cat['blogName'] ) ? $cat['blogName'] . '/' : '' ) . $cat['langCode'] . ']' . ( ( isset( $cat['isDefault'] ) && $cat['isDefault'] ) ? ' - ' . __( 'default' ) : '' ) ?></option>
											<?php endforeach ?>
										<?php endif ?>
									</select>
								</div>
							</div>
							
							<div class="dropdown-divider"></div>

							<div class="col-sm-6">
								<div class="form-group">
									<label><?php echo __( 'target' ) ?></label>
									<select id="toCategory" class="form-control">
										<option value=""><?php echo __( 'select' ) ?>...</option>
										<option value="0"><?php echo __( 'convert-posts-into-pages' ) ?>...</option>
										<?php if ( !empty( $cats ) ) :
											foreach( $cats as $cat ) : ?>
											<option value="<?php echo $cat['id'] ?>"><?php echo $cat['name'] ?><?php echo ' [' . ( !empty( $cat['blogName'] ) ? $cat['blogName'] . '/' : '' ) . $cat['langCode'] . ']' . ( ( isset( $cat['isDefault'] ) && $cat['isDefault'] ) ? ' - ' . __( 'default' ) : '' ) ?></option>
											<?php endforeach ?>
										<?php endif ?>
									</select>
								</div>
							</div>
						</div>
						
						<?php if ( $hasSites ) : ?>
						<div id="targetSiteDiv" class="row mb-3 d-none">
							<div class="col-sm-4">
								<div class="form-group">
									<label><?php echo __( 'choose-site' ) ?></label>
									<select id="fromSite" class="form-control">
										<option value=""><?php echo __( 'select' ) ?>...</option>
										<option value="<?php echo SITE_ID ?>"><?php echo $Admin->DefaultSiteName() ?></option>
										<?php if ( !empty( $Sites ) ) :
											foreach( $Sites as $singeSite ) : ?>
										<option value="<?php echo $singeSite['id'] ?>"><?php echo $singeSite['title'] ?></option>
											<?php endforeach ?>
										<?php endif ?>
									</select>
								</div>
							</div>
							
							<div class="dropdown-divider"></div>

							<div class="col-sm-4">
								<div class="form-group">
									<label><?php echo __( 'target' ) ?></label>
									<select id="toSite" class="form-control">
										<option value=""><?php echo __( 'select' ) ?>...</option>

										<option value="<?php echo SITE_ID ?>"><?php echo $Admin->DefaultSiteName() ?></option>
										
										<?php if ( !empty( $Sites ) ) :
											
											foreach( $Sites as $singeSite ) : ?>
											<option value="<?php echo $singeSite['id'] ?>"><?php echo $singeSite['title'] ?></option>
											<?php endforeach ?>
										<?php endif ?>
									</select>
								</div>
							</div>
							<small id="toSiteHelp" class="form-text text-muted"><?php echo __( 'bulk-move-posts-site-tip' ) ?></small>
						</div>
						<?php endif ?>
						
						<?php if ( $hasLangs ) : ?>
						<div id="targetLangDiv" class="row mb-3 d-none">
							<div class="col-sm-4">
								<div class="form-group">
									<label><?php echo __( 'choose-language' ) ?></label>
									<select id="fromLang" class="form-control">
										<option value=""><?php echo __( 'select' ) ?>...</option>
										<option value="<?php echo $Admin->DefaultLang()['id'] ?>"><?php echo $Admin->DefaultLang()['title'] ?></option>
										
										<?php if ( !empty( $Langs ) ) :
											foreach( $Langs as $lId => $lData ) : ?>
										<option value="<?php echo $lId ?>"><?php echo $lData['lang']['title'] ?></option>
											<?php endforeach ?>
										<?php endif ?>
									</select>
								</div>
							</div>
							
							<div class="dropdown-divider"></div>

							<div class="col-sm-4">
								<div class="form-group">
									<label><?php echo __( 'target' ) ?></label>
									<select id="toLang" class="form-control">
										<option value=""><?php echo __( 'select' ) ?>...</option>
										<option value="<?php echo $Admin->DefaultLang()['id'] ?>"><?php echo $Admin->DefaultLang()['title'] ?></option>

										<?php if ( !empty( $Langs ) ) :
											foreach( $Langs as $lId => $lData ) : ?>
										<option value="<?php echo $lId ?>"><?php echo $lData['lang']['title'] ?></option>
											<?php endforeach ?>
										<?php endif ?>
									</select>
								</div>
							</div>
							<small id="toSiteHelp" class="form-text text-muted"><?php echo __( 'bulk-move-posts-lang-tip' ) ?></small>
						</div>
						<?php endif ?>
						
						<?php if ( $hasBlogs ) : ?>
						<div id="targetBlogDiv" class="row mb-3 d-none">
						
							<div class="col-sm-2">
								<div class="form-group">
									<label><?php echo __( 'choose-blog' ) ?></label>
									<select id="fromBlog" class="form-control">
										<option value=""><?php echo __( 'select' ) ?>...</option>
										<?php if ( !empty( $Blogs ) ) :
											foreach( $Blogs as $bId => $bData ) : ?>
										<option value="<?php echo $bData['id_blog'] ?>"><?php echo $bData['name'] ?></option>
											<?php endforeach ?>
										<?php endif ?>
									</select>
								</div>
							</div>
							
							<div class="dropdown-divider"></div>

							<div class="col-sm-6">
								<div class="form-group">
									<label><?php echo __( 'target' ) ?></label>
									<select id="toBlog" class="form-control">
										<option value=""><?php echo __( 'select' ) ?>...</option>
										<option value="0"><?php echo __( 'orphan-content-target' ) ?></option>
										<?php if ( !empty( $Blogs ) ) :
											foreach( $Blogs as $bId => $bData ) : ?>
										<option value="<?php echo $bData['id_blog'] ?>"><?php echo $bData['name'] ?></option>
											<?php endforeach ?>
										<?php endif ?>
									</select>
								</div>
							</div>
							
							<div class="dropdown-divider"></div>
							
							<div id="blogPostSelection" class="col-sm-4 d-none">
								<div class="form-group">
									<label><?php echo __( 'options' ) ?></label>
									<select id="toBlogOptions" class="form-control">
										<option value="move"><?php echo __( 'move-categories-move-posts' ) ?></option>
										<option value="copy"><?php echo __( 'copy-categories-move-posts' ) ?></option>
										<option value="default"><?php echo __( 'move-posts-default-category' ) ?></option>
									</select>
								</div>
							</div>
							
						</div>
						<?php endif ?>
						
						<div class="col-md-12">
							<div id="progressBarDiv" class="progress mb-3 d-none">
								<div class="progress-bar bg-success progress-bar-striped" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%"></div>
							</div>
							
							<div id="post-status"></div>
								
							<div id="post-success" class="alert alert-info d-none">
								<?php echo __( 'move-executed-successfully' ) ?>
							</div>
							
							<div id="post-no-content" class="alert alert-warning d-none">
								<?php echo __( 'no-posts-found' ) ?>
							</div>
							
							<div id="post-error" class="alert alert-danger d-none">
								<?php echo __( 'move-executed-error' ) ?>
							</div>
						</div>
					</div>
				</div>
			
				<hr />
			
				<div class="form-row">
					<div class="form-group col-md-6">
						<input type="hidden" name="__d" value="">

						<div class="align-middle">
							<div class="float-left mt-1">
								<button id="moveButton" class="btn btn-primary btn-sm"><?php echo __( 'move-posts' ) ?></button>
								<a class="btn btn-secondary btn-sm" href="<?php echo $Admin->Url() ?>" role="button"><?php echo __( 'cancel' ) ?></a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>