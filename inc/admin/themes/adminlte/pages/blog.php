<?php
	include ( ARRAYS_ROOT . 'generic-arrays.php');
	$bGroup = ( !empty( $Blog['groups_data'] ) ? Json( $Blog['groups_data'] ) : null );
	$trans = $Blog['translation'];
?>
<div class="row">
  <div class="col-12">
  <div class="card">
  <div class="card-body">
	<?php if ( !$Admin->IsDefaultLang() ) : ?>
	<div class="alert alert-primary" role="alert">
		<?php echo sprintf( __( 'edit-s-translation' ), $Admin->CurrentLang()['lang']['title'] ) ?>. <?php echo __( 'edit-blog-other-lang' ) ?>
	</div>
	<?php endif ?>
		<form class="tab-content" id="form" method="post" action="" autocomplete="off">
		<div class="form-row">
		<div class="form-group col-md-6">
			<h4><?php echo $L['blog-settings'] ?></h4>

			<div class="form-group">
				<label for="inputTitle"><?php echo $L['name'] ?></label>
				<input type="text" class="form-control" name="title" id="inputTitle" value="<?php echo ( $Admin->IsDefaultLang() ? htmlspecialchars( $Blog['name'] ) : ( isset( $trans['name'] ) ? htmlspecialchars( $trans['name'] ) : '' ) ) ?>">
				<small id="titleHelp" class="form-text text-muted"><?php echo $L['the-title-how-it-appears'] ?></small>
			</div>

			<div class="form-group">
				<label for="inputSlug"><?php echo $L['slug'] ?></label>
				<input type="text" class="form-control" id="inputSlug" name="slug" value="<?php echo htmlspecialchars( $Blog['sef'] ) ?>">
				<small id="slugHelp" class="form-text text-muted"><?php echo $L['slug-tip'] ?></small>
			</div>

			<div class="form-group">
				<label for="inputSlogan"><?php echo $L['blog-slogan'] ?></label>
				<input type="text" class="form-control" id="inputSlogan" name="slogan" value="<?php echo ( $Admin->IsDefaultLang() ? htmlspecialchars( $Blog['slogan'] ) : ( isset( $trans['slogan'] ) ? htmlspecialchars( $trans['slogan'] ) : '' ) ) ?>">
				<small id="sloganHelp" class="form-text text-muted"><?php echo $L['blog-slogan-tip'] ?></small>
			</div>

			<div class="form-group">
				<label for="inputDescription"><?php echo $L['description'] ?></label>
				<textarea class="form-control" id="inputDescription" name="description" rows="3"><?php echo ( $Admin->IsDefaultLang() ? htmlspecialchars( $Blog['description'] ) : ( isset( $trans['description'] ) ? htmlspecialchars( $trans['description'] ) : '' ) ) ?></textarea>
				<small id="inputDescription" class="form-text text-muted"><?php echo $L['descr-tip'] ?></small>
			</div>
			
			<div class="form-group">
				<label for="inputRedirection"><?php echo $L['redirect-blog'] ?></label>
				<input type="text" class="form-control" id="inputRedirection" name="redirect" value="<?php echo htmlspecialchars( $Blog['redirect'] ) ?>" placeholder="https://">
				<small id="inputRedirection" class="form-text text-muted"><?php echo $L['redirect-blog-tip'] ?></small>
			</div>
			
			<div class="form-group">
				<label for="inputEnabled"><?php echo $L['blog-is-enabled'] ?></label>
				<select name="select-lang" class="form-control selectpicker" id="slcCountry" >
					<option value="0" <?php echo ( ( $Blog['id_lang'] == 0 ) ? 'selected' : '' ) ?>><?php echo $L['everywhere'] ?></option>
					<?php $langs = Langs( $Admin->GetSite(), false, false );
					if ( !empty( $langs ) ) :
						foreach ( $langs as $lang ):
					?>
					<option value="<?php echo $lang['id'] ?>" data-flag="<?php echo SITE_URL . 'languages' . PS . 'flags' . PS . $key . '.png' ?>" <?php echo ( ( $Blog['id_lang'] == $lang['id'] ) ? 'selected' : '' ) ?>><?php echo sprintf($L['on-lang-only'], $lang['title'] ) ?></option>
					<?php endforeach; endif; ?>
				</select>
				<small id="inputEnabledHelp" class="form-text text-muted"><?php echo $L['blog-is-enabled-tip'] ?></strong></small>
			</div>
			
			<div class="form-group">
				<label for="inputTheme"><?php echo $L['blog-theme'] ?></label>
				<select name="select-theme" class="form-control selectpicker" id="slcCountry" >
					<option value="" <?php echo ( ( $Blog['theme'] == '' ) ? 'selected' : '' ) ?>><?php echo $L['default-theme'] ?></option>
				<?php $themes = LoadThemes( 'normal', false );
				if ( !empty( $themes ) ) :
					
					$defTheme = Settings::Get()['theme'];
					
					foreach ( $themes as $_theme => $theme ) : 
					
						if ( $defTheme == $_theme )
							continue;
					?>
					<option value="<?php echo $_theme ?>" <?php echo ( ( $Blog['theme'] == $_theme ) ? 'selected' : '' ) ?>><?php echo $theme['title'] ?></option>
				<?php endforeach; endif; ?>
				</select>
				<small id="inputThemeHelp" class="form-text text-muted"><?php echo $L['blog-theme-tip'] ?></strong></small>
			</div>
			
			<div class="form-group">
				<label for="inputType"><?php echo $L['select-type-blog'] ?></label>
				<select  name="select-type" class="form-control selectpicker" id="slcCountry" >
					<?php foreach( $blogTypesArray as $key => $row ) : ?>
					<option value="<?php echo $key ?>" <?php echo ( ( $Blog['type'] == $key ) ? 'selected' : '' ) ?>><?php echo $row['title'] ?></option>
					<?php endforeach ?>
				</select>
				<small id="inputTypeHelp" class="form-text text-muted"><?php echo $L['select-type-blog-tip'] ?></strong></small>
			</div>
			
			<div class="form-group">
				<label for="inputHomeTemplate"><?php echo __( 'custom-home-template' ) ?></label>
				<input type="text" class="form-control" id="inputHomeTemplate" name="home-template" value="<?php echo htmlspecialchars( $Blog['custom_home_tmp'] ) ?>" placeholder="my-custom-template-filename">
				<small id="inputHomeTemplateHelp" class="form-text text-muted"><?php echo $L['custom-home-template-tip'] ?></small>
			</div>
			
			<div class="form-group">
				<label for="inputListTemplate"><?php echo __( 'custom-list-template' ) ?></label>
				<input type="text" class="form-control" id="inputListTemplate" name="list-template" value="<?php echo htmlspecialchars( $Blog['custom_list_tmp'] ) ?>" placeholder="my-custom-template-filename">
				<small id="inputListTemplateHelp" class="form-text text-muted"><?php echo $L['custom-list-template-tip'] ?></small>
			</div>
			
			<div class="form-group">
				<label for="inputPostTemplate"><?php echo __( 'custom-post-template' ) ?></label>
				<input type="text" class="form-control" id="inputPostTemplate" name="post-template" value="<?php echo htmlspecialchars( $Blog['custom_post_tmp'] ) ?>" placeholder="my-custom-template-filename">
				<small id="inputPostTemplateHelp" class="form-text text-muted"><?php echo $L['custom-post-template-tip'] ?></small>
			</div>
			
			<div class="form-check">
				<input class="form-check-input" type="checkbox" value="1" name="frontpage" <?php echo ( $Blog['frontpage'] ? 'checked' : '' ) ?>>
				<label class="form-check-label" for="frontpageBlog">
					<?php echo $L['show-on-frontpage'] ?>
				</label>
				<small id="frontpageBlogHelp" class="form-text text-muted"><?php echo $L['show-blog-on-frontpage-tip'] ?></small>
			</div>
			
			<div class="form-group">
				<label for="inputFrontpageShows"><?php echo $L['blog-frontpage-displays'] ?></label>
				<select  name="frontpage-shows" class="form-control selectpicker" id="slcCountry" >
					<option value="posts" <?php echo ( ( $Blog['frontpage_shows'] == 'posts' ) ? 'selected' : '' ) ?>><?php echo $L['your-latest-posts'] ?></option>
					<option value="page" <?php echo ( ( $Blog['frontpage_shows'] == 'page' ) ? 'selected' : '' ) ?>><?php echo $L['a-static-page-select-below'] ?></option>
				</select>
				<small id="inputFrontpageShowsHelp" class="form-text text-muted"><?php echo $L['select-blog-frontpage-tip'] ?></small>
			</div>
			
			<div class="form-group" id="inputFrontpagePosts">
				<label for="inputFrontpagePosts"><?php echo $L['limit-number-of-posts'] ?></label>
				<input value="<?php echo ( empty( $Blog['article_limit'] ) ? $Admin->Settings()::Get()['article_limit'] : $Blog['article_limit'] ) ?>" type="number" class="form-control border-width-2" name="article_limit" step="any" min="1" max="100" >
				<small id="inputFrontpagePostsHelp" class="form-text text-muted"><?php echo $L['limit-number-of-posts-blog-tip'] ?></small>
			</div>
			
			<div class="form-group" id="inputFrontpagePage">
				<label for="inputFrontpagePage"><?php echo $L['homepage'] ?></label>
				<select  name="frontpage-page" class="form-control selectpicker" id="slcCountry" >
				<option value="">---</option>
				<?php 
					if ( !empty( $BlogPages ) ) :
						foreach( $BlogPages as $page ) : ?>
					<option value="<?php echo $page['id_post'] ?>" <?php echo ( ( $Blog['frontpage_page'] == $page['id_post'] ) ? 'selected' : '' ) ?>><?php echo $page['title'] ?></option>
				<?php endforeach; endif; ?>
				</select>
				<small id="inputFrontpagePageHelp" class="form-text text-muted"><?php echo $L['select-blog-static-page-tip'] ?></small>
			</div>
			
			<div class="form-check">
				<input class="form-check-input" type="checkbox" value="1" name="sitemap" <?php echo ( $Blog['news_sitemap'] ? 'checked' : '' ) ?>>
				<label class="form-check-label" for="sitemapBlog">
					<?php echo $L['enable-in-news-sitemap'] ?>
				</label>
				<small id="sitemapBlogHelp" class="form-text text-muted"><?php echo $L['enable-in-news-sitemap-tip'] ?></small>
			</div>
			
			<div class="form-check">
				<input class="form-check-input" type="checkbox" value="1" name="hide_sitemap" <?php echo ( $Blog['hide_sitemap'] ? 'checked' : '' ) ?>>
				<label class="form-check-label" for="sitemapExcBlog">
					<?php echo $L['exclude-blog-from-sitemap'] ?>
				</label>
				<small id="sitemapExcBlogHelp" class="form-text text-muted"><?php echo $L['exclude-blog-from-sitemap-tip'] ?></small>
			</div>
			
			<div class="form-check">
				<input class="form-check-input" id="dontLoadPosts" type="checkbox" value="1" name="dont_load_posts" <?php echo ( $Blog['dont_load_posts'] ? 'checked' : '' ) ?>>
				<label class="form-check-label" for="dontLoadPosts">
					<?php echo $L['dont-load-posts-frontpage'] ?>
				</label>
				<small id="dontLoadPostsHelp" class="form-text text-muted"><?php echo $L['dont-load-posts-frontpage-tip'] ?></small>
			</div>
			
			<div class="form-check">
				<input class="form-check-input" type="checkbox" value="1" name="enable_rss" <?php echo ( $Blog['enable_rss'] ? 'checked' : '' ) ?>>
				<label class="form-check-label" for="enableRssBlog">
					<?php echo $L['enable-rss'] ?>
				</label>
				<small id="enableRssBlogHelp" class="form-text text-muted"><?php echo $L['rss-blog-tip'] ?></small>
			</div>
			
			<div class="form-check">
				<input class="form-check-input" type="checkbox" value="1" name="disable" <?php echo ( $Blog['disabled'] ? 'checked' : '' ) ?>>
				<label class="form-check-label" for="disableBlog">
					<?php echo $L['disable'] ?>
				</label>
				<small id="disableBlogHelp" class="form-text text-muted"><?php echo $L['disable-blog-tip'] ?></small>
			</div>
			
			<div class="form-group">
				<label for="membergroups"><?php echo $L['membergroups'] ?></label>
				<select  name="membergroups[]" class="form-control select2 form-select shadow-none mt-3" multiple id="slcAmp" >
					<?php $groups = AdminGroups( $Admin->GetSite(), false );
						if ( !empty( $groups ) ) :
							foreach( $groups as $group ) : ?>
							<option  value="<?php echo $group['id_group'] ?>" <?php echo ( ( !empty( $bGroup ) && in_array( $group['id_group'], $bGroup ) ) ? 'selected' : '' ) ?>><?php echo $group['group_name'] ?></option>
						<?php endforeach ?>
					<?php endif ?>
				</select>
				<small id="membergroupsHelp" class="form-text text-muted"><?php echo $L['select-blog-membergroup-tip'] ?></small>
			</div>

		</div>
		</div>
		
		<div class="form-row">
			<div class="form-group col-md-6">
				<hr />
				<div class="form-check">
					<input class="form-check-input" type="checkbox" value="1" name="delete" id="deleteCheckBox" >
					<label class="form-check-label" for="deleteCheckBox">
						<?php echo $L['delete'] ?>
					</label>
					<small id="deleteCheckBoxHelp" class="form-text text-muted"><?php echo $L['delete-blog-tip'] ?></small>
				</div>

				<input type="hidden" name="_token" value="<?php echo generate_token( 'editBlog' . $Blog['id_blog'] ) ?>">
				
				<div class="align-middle">
					<div class="float-left mt-1">
						<button type="submit" class="btn btn-primary btn-sm" name="save"><?php echo $L['save'] ?></button>
						<a class="btn btn-secondary btn-sm" href="<?php echo $Admin->GetUrl( 'blogs' ) ?>" role="button"><?php echo $L['cancel'] ?></a>
					</div>
				</div>
			</div>
		</div>
		</form>
	</div>
</div>
</div>
</div>