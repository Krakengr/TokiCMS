<div class="container-fluid">
	<div class="row">
		<form class="tab-content" id="form" method="post" action="" autocomplete="off">
		<div class="form-row">
		<div class="form-group col-md-6">
			<h4><?php echo $L['blog-settings'] ?></h4>
				
			<div class="form-group">
				<label for="inputTitle"><?php echo $L['name'] ?></label>
				<input type="text" class="form-control" name="title" id="inputTitle" value="<?php echo htmlspecialchars( $data['name'] ) ?>">
				<small id="titleHelp" class="form-text text-muted"><?php echo $L['the-title-how-it-appears'] ?></small>
			</div>

			<div class="form-group">
				<label for="inputSlug"><?php echo $L['slug'] ?></label>
				<input type="text" class="form-control" id="inputSlug" name="slug" value="<?php echo htmlspecialchars( $data['sef'] ) ?>">
				<small id="titleHelp" class="form-text text-muted"><?php echo $L['slug-tip'] ?></small>
			</div>

			<div class="form-group">
				<label for="inputSlogan"><?php echo $L['blog-slogan'] ?></label>
				<input type="text" class="form-control" id="inputTitle" name="slogan" value="<?php echo htmlspecialchars( $data['slogan'] ) ?>">
				<small id="titleHelp" class="form-text text-muted"><?php echo $L['blog-slogan-tip'] ?></small>
			</div>

			<div class="form-group">
				<label for="inputDescription"><?php echo $L['description'] ?></label>
				<textarea class="form-control" id="inputTitle" name="description" rows="3"><?php echo htmlspecialchars( $data['description'] ) ?></textarea>
				<small id="titleHelp" class="form-text text-muted"><?php echo $L['descr-tip'] ?></strong></small>
			</div>
			
			<div class="form-group">
				<label for="inputRedirection"><?php echo $L['redirect-blog'] ?></label>
				<input type="text" class="form-control" id="inputTitle" name="redirect" value="<?php echo htmlspecialchars( $data['redirect'] ) ?>">
				<small id="titleHelp" class="form-text text-muted"><?php echo $L['redirect-blog-tip'] ?></strong></small>
			</div>
			
			<div class="form-group">
				<label for="inputEnabled"><?php echo $L['blog-is-enabled'] ?></label>
				<select  name="select-lang" class="form-control selectpicker" id="slcCountry" >
					<option value="0" <?php echo ( ( $data['id_lang'] == 0 ) ? 'selected' : '' ) ?>><?php echo $L['everywhere'] ?></option>
					<?php $langsForm = Langs( $Admin::GetSite(), false, false ); 
					if ( !empty( $langsForm ) ) :
						foreach ( $langsForm as $key => $lang ):
					?>
					<option value="<?php echo $lang['id'] ?>" data-flag="<?php echo SITE_URL . 'languages' . PS . 'flags' . PS . $key . '.png' ?>" <?php echo ( ( $data['id_lang'] == $lang['id'] ) ? 'selected' : '' ) ?>><?php echo sprintf($L['on-lang-only'], $lang['title'] ) ?></option>
					<?php endforeach; endif; ?>
				</select>
				<small id="titleHelp" class="form-text text-muted"><?php echo $L['blog-is-enabled-tip'] ?></strong></small>
			</div>
			
			<div class="form-group">
				<label for="inputType"><?php echo $L['blog-theme'] ?></label>
				<select  name="select-theme" class="form-control selectpicker" id="slcCountry" >
					<option value="0" <?php echo ( ( $data['id_theme'] == 0 ) ? 'selected' : '' ) ?>><?php echo $L['default-theme'] ?></option>
				</select>
				<small id="titleHelp" class="form-text text-muted"><?php echo $L['blog-theme-tip'] ?></strong></small>
			</div>
			
			<div class="form-group">
				<label for="inputType"><?php echo $L['select-type-blog'] ?></label>
				<select  name="select-type" class="form-control selectpicker" id="slcCountry" >
					<?php foreach( $blogTypesArray as $key => $row ) : ?>
					<option value="<?php echo $key ?>" <?php echo ( ( $data['type'] == $key ) ? 'selected' : '' ) ?>><?php echo $row['title'] ?></option>
					<?php endforeach ?>
				</select>
				<small id="titleHelp" class="form-text text-muted"><?php echo $L['select-type-blog-tip'] ?></strong></small>
			</div>
			
			<div class="form-check">
				<input class="form-check-input" type="checkbox" value="1" name="frontpage" <?php echo ( $data['frontpage'] ? 'checked' : '' ) ?>>
				<label class="form-check-label" for="frontpageBlog">
					<?php echo $L['show-on-frontpage'] ?>
				</label>
				<small id="titleHelp" class="form-text text-muted"><?php echo $L['show-blog-on-frontpage-tip'] ?></small>
			</div>
			
			<div class="form-group">
				<label for="inputType"><?php echo $L['blog-frontpage-displays'] ?></label>
				<select  name="frontpage-shows" class="form-control selectpicker" id="slcCountry" >
					<option value="posts" <?php echo ( ( $data['frontpage_shows'] == 'posts' ) ? 'selected' : '' ) ?>><?php echo $L['your-latest-posts'] ?></option>
					<option value="page" <?php echo ( ( $data['frontpage_shows'] == 'page' ) ? 'selected' : '' ) ?>><?php echo $L['a-static-page-select-below'] ?></option>
				</select>
				<small id="titleHelp" class="form-text text-muted"><?php echo $L['select-blog-frontpage-tip'] ?></strong></small>
			</div>
			
			<div class="form-group">
				<label for="inputType"><?php echo $L['homepage'] ?></label>
				<select  name="frontpage-page" class="form-control selectpicker" id="slcCountry" >
				<option value="">---</option>
					<?php if ( !empty( $blogPages ) ) : 
						foreach( $blogPages as $key => $p ) : ?>
					<option value="<?php echo $p::PostID() ?>" <?php echo ( ( $data['frontpage_page'] == $key ) ? 'selected' : '' ) ?>><?php echo $p::Title() ?></option>
					<?php endforeach; unset( $blogPages ); endif; ?>
				</select>
				<small id="titleHelp" class="form-text text-muted"><?php echo $L['select-blog-static-page-tip'] ?></strong></small>
			</div>
			
			<div class="form-check">
				<input class="form-check-input" type="checkbox" value="1" name="sitemap" <?php echo ( $data['news_sitemap'] ? 'checked' : '' ) ?>>
				<label class="form-check-label" for="sitemapBlog">
					<?php echo $L['enable-in-news-sitemap'] ?>
				</label>
				<small id="titleHelp" class="form-text text-muted"><?php echo $L['enable-in-news-sitemap-tip'] ?></small>
			</div>
			
			<div class="form-check">
				<input class="form-check-input" type="checkbox" value="1" name="enable_rss" <?php echo ( $data['enable_rss'] ? 'checked' : '' ) ?>>
				<label class="form-check-label" for="enableRssBlog">
					<?php echo $L['enable-rss'] ?>
				</label>
				<small id="titleHelp" class="form-text text-muted"><?php echo $L['rss-blog-tip'] ?></small>
			</div>
			
			<div class="form-check">
				<input class="form-check-input" type="checkbox" value="1" name="disable" <?php echo ( $data['disabled'] ? 'checked' : '' ) ?>>
				<label class="form-check-label" for="disableBlog">
					<?php echo $L['disable'] ?>
				</label>
				<small id="titleHelp" class="form-text text-muted"><?php echo $L['disable-blog-tip'] ?></small>
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
				<small id="titleHelp" class="form-text text-muted"><?php echo $L['delete-blog-tip'] ?></small>
			</div>

			<input type="hidden" name="_token" value="<?php echo generate_token( 'editBlog' . $data['id_blog'] ) ?>">
			
			<div class="align-middle">
				<div class="float-left mt-1">
					<button type="submit" class="btn btn-primary btn-sm" name="save"><?php echo $L['save'] ?></button>
					<a class="btn btn-secondary btn-sm" href="<?php echo $Admin->Url() ?>" role="button"><?php echo $L['cancel'] ?></a>
				</div>
			</div>
		</div>
		</div>
		</form>
	</div>
</div>