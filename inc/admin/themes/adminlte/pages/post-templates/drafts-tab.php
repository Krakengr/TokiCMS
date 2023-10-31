<?php $dr = Drafts( $Post->PostID(), $Admin->UserID() ) ?>

<h5><?php echo $L['drafts'] ?></h5>
<div class="mb-3">
	<div class="form-group row">
		<label for="draftsList" class="col-sm-2 col-form-label"><?php __( 'drafts-list' ) ?></label>
		<div class="col-md-12">
			<div class="table-responsive">				
				<table id="postDraftsTable" class="table table-striped table-bordered table-hover">
					<thead>
						<tr>
							<th class="text-center" width="60%"><?php echo __( 'title' ) ?></th>
							<th class="text-center" width="40%"><?php echo __( 'draft-last-saved' ) ?></th>
						</tr>
					</thead>
					<tbody>
					<?php if ( !empty( $dr ) ) :
						$draftId = $Admin->GetDraft();
						
						foreach ( $dr as $draft ) : 
							
							if ( $draftId == $draft['id'] )
							{
								$url = $draft['title'];
							}
							
							else
							{
								$url = '<a href="' . str_replace( '{draftid}', $draft['id'], $PostEditDraftUri ) . '" target="_blank" rel="noreferrer noopener">' . $draft['title'] . '</a>';
							}
							
							if( !empty( $draft['edited_time'] ) ) 
							{
								$date = postDate( $draft['edited_time'], false, null, true );
							}
							else
							{
								$date = postDate( $draft['added_time'], false, null, true );;
							}
							
							if ( $draft['draft_type'] == 'auto' )
							{
								$date .= ' <em>(' . __( 'autosave' ) . ')</em>';
							}
						?>
						<tr id="draftField-row<?php echo $draft['id'] ?>">
							<td id="url" class="text-center"><?php echo $url ?></td>
							<td class="text-center"><?php echo $date ?></td>
						</tr>
					<?php endforeach ?>
					<?php endif ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>