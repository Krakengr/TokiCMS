<?php $links = GetCheckedLinks( $Admin->GetSite() ) ?>
<div class="row">
  <div class="col-12">
	<div class="card mb-4">
		<div class="card-header">
			<?php echo $L['categories'] ?>
		</div>
		<div class="card-body">
        <table class="table table-hover mb-0" id="linksDatatable" style="table-layout: fixed; width: 100% !important;">
          <thead>
            <tr>
              <th class="text-center"><?php echo $L['url'] ?></th>
              <th class="text-center"><?php echo $L['status'] ?></th>
              <th class="text-center"><?php echo $L['link-text'] ?></th>
			  <th class="text-center"><?php echo $L['source'] ?></th>
			  <th class="text-center"><?php echo $L['details'] ?></th>
            </tr>
          </thead>
          <tbody>
		  <?php 
			if ( !empty( $links ) ) :
				foreach ( $links as $link ) :
					if ( !empty( $link['title'] ) )
					{
						$text = '<a href="' . $Admin->GetUrl( 'edit-post' . PS . 'id' . PS . $link['id_post'] ) . '">' . $link['title'] . '</a>';
					}
					elseif ( !empty( $link['id_comment'] ) )
					{
						$text = '<a href="' . $Admin->GetUrl( 'edit-comments' . PS . 'id' . PS . $link['id_comment'] ) . '">' . $link['link_text'] . '</a>';
					}
					else
					{
						$text = $link['link_text'];
					}
			?>
            <tr>
				<td class="text-center"><a href="<?php echo $link['url'] ?>" class="text-decoration-none text-reset fw-bolder" target="_blank" rel="noopener noreferrer"><?php echo $link['url'] ?></a></td>
				<td class="text-center"><?php echo $link['url_status'] ?></td>
				<td class="text-center"><?php echo $link['link_text'] ?></td>
				<td class="text-center"><?php echo $text ?></td>
				<td class="text-center"><?php if ( !empty( $link['response_headers'] ) ) : ?><a href="#" type="button" data-toggle="tooltip" data-placement="right" data-html="true" title="<?php echo str_replace( '"', '&quot;', $link['response_headers'] ) ?>"><i class="bi bi-info-circle"></i></a><?php endif ?></td>
            </tr>
		<?php endforeach ?>
		<?php endif ?>
          </tbody>
        </table>
				</div>
				
				<div class="card-footer"></div>
			</div>
		</div>
	</form>
</div>