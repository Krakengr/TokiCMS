<?php
	require ( ARRAYS_ROOT . 'forms-arrays.php');

	$elementsLeft = $elementsRight = array();
	
	$elementsTotal = count( $genericFormsArray );
	$elementsNum = ( ( $elementsTotal > 0 ) ? ceil( ( $elementsTotal / 2 ) ) : 0 );
	
	if ( $elementsTotal > 10 )
	{
		$elementsLeft  = array_slice( $genericFormsArray, 0, $elementsNum );
		$elementsRight = array_slice( $genericFormsArray, $elementsNum, $elementsTotal );
	}
	else
	{
		$elementsLeft = $genericFormsArray;
	}
?>
<div class="row mb-5">
	
	<div class="col-md-6">
	
	
	
	<div class="card card-primary card-outline card-tabs">
		<div class="card-header p-0 pt-1">
			<ul class="nav nav-tabs" id="form-settings-tabs" role="tablist">
				<li class="nav-item">
				<a class="nav-link active" id="elements-tab" data-toggle="pill" href="#elements" role="tab" aria-controls="elements" aria-selected="true"><?php echo __( 'elements' ) ?></a>
				</li>
				<li class="nav-item">
				<a class="nav-link" id="form-options-tab" data-toggle="pill" href="#form-options" role="tab" aria-controls="form-options" aria-selected="false"><?php echo __( 'form-options' ) ?></a>
				</li>
				<li class="nav-item">
				<a class="nav-link" id="notifications-tab" data-toggle="pill" href="#notifications" role="tab" aria-controls="notifications" aria-selected="false"><?php echo __( 'notifications' ) ?></a>
				</li>
				<li class="nav-item">
				<a class="nav-link" id="confirmations-tab" data-toggle="pill" href="#confirmations" role="tab" aria-controls="confirmations" aria-selected="false"><?php echo __( 'confirmations' ) ?></a>
				</li>
				<li class="nav-item">
				<a class="nav-link" id="template-tab" data-toggle="pill" href="#template" role="tab" aria-controls="template" aria-selected="false"><?php echo __( 'template' ) ?></a>
				</li>
			</ul>
		</div>
		<form class="tab-content" id="form" method="post" action="" autocomplete="off">
		
		<div class="card-body">
			<div class="tab-content" id="form-settings-tabsContent">
				<div class="tab-pane fade show active" id="elements" role="tabpanel" aria-labelledby="elements-tab">
					<div class="row">
					
					<?php if ( !empty( $elementsLeft ) ) : ?>
						<div class="col-sm-6">
						<?php foreach ( $elementsLeft as $w => $elem ) : ?>
						<div class="card" id="<?php echo $w ?>">
							<div class="card-header border-transparent">
								<h4 class="card-title"><?php echo $elem['title'] ?></h4>
								<div class="card-tools">
									<div class="btn-group">
										<button type="button" id="addElementButton" data-id="<?php echo $w ?>" class="btn btn-tool addElementButton">
											<i class="fas fa-plus"></i>
										</button>
									</div>
								</div>
							  </div>
						</div>
						<?php endforeach ?>
						</div>
					<?php endif ?>
					
					<?php if ( !empty( $elementsRight ) ) : ?>
						<div class="col-sm-6">
							<?php foreach ( $elementsRight as $w => $elem ) : ?>
							<div class="card" data-id="<?php echo $w ?>">
								<div class="card-header border-transparent">
									<h4 class="card-title"><?php echo $widget['title'] ?></h4>
									<div class="card-tools">
										<div class="btn-group">
											<button type="button" id="addElementButton" data-id="<?php echo $w ?>" class="btn btn-tool addElementButton">
												<i class="fas fa-plus"></i>
											</button>
										</div>
									</div>
								  </div>
							</div>
						<?php endforeach ?>
						</div>
						<?php endif ?>

					</div>
				</div>
				
				<div class="tab-pane fade" id="form-options" role="tabpanel" aria-labelledby="form-options-tab">
					<div class="form-group">
						<label for="formName"><?php echo __( 'name' ) ?></label>
						<input class="form-control" type="text" id="formName" name="title" value="<?php echo $Form['name'] ?>">
					</div>
					
					<div class="form-group">
						<label for="inputFrontpagePage"><?php echo __( 'membergroups' ) ?></label>
						<select  name="membergroups[]" class="form-control select2 form-select shadow-none mt-3" multiple id="slcAmp" >
							<?php $groups = AdminGroups( $Admin->GetSite(), false );
								if ( !empty( $groups ) ) :
									foreach( $groups as $group ) : ?>
									<option  value="<?php echo $group['id_group'] ?>" <?php echo ( ( !empty( $Form['groups'] ) && in_array( $group['id_group'], $Form['groups'] ) ) ? 'selected' : '' ) ?>><?php echo $group['group_name'] ?></option>
								<?php endforeach ?>
							<?php endif ?>
						</select>
						<small id="membergroupsHelp" class="form-text text-muted"><?php echo __( 'select-form-membergroup-tip' ) ?></small>
					</div>
					
					<div class="form-group">
						<label for="formCss"><?php echo __( 'form-css-class' ) ?></label>
						<input class="form-control" type="text" id="formCss" name="form-css" value="<?php echo ( isset( $Form['data']['form_css'] ) ? $Form['data']['form_css'] : '' ) ?>">
						<small id="formCssHelp" class="form-text text-muted"><?php echo __( 'form-css-class-tip' ) ?></small>
					</div>
					
					<div class="form-check">
						<input id="enableAntiSpamBox" class="form-check-input" type="checkbox" value="1" name="anti-spam" <?php echo ( ( isset( $Form['data']['anti_spam'] ) && $Form['data']['anti_spam'] ) ? 'checked' : '' ) ?> />
						<label class="form-check-label" for="enableAntiSpamBox">
							<?php echo __( 'enable-anti-spam-protection' ) ?>
						</label>
						<small id="enableAntiSpamBoxHelp" class="form-text text-muted"><?php echo __( 'enable-anti-spam-protection-tip' ) ?></small>
					</div>
					
					<hr />
					
					<div class="form-check">
						<input id="disableCheckBox" class="form-check-input" type="checkbox" value="1" name="disable" <?php echo ( ( $Form['disabled'] == 1 ) ? 'checked' : '' ) ?> />
						<label class="form-check-label" for="disableCheckBox">
							<?php echo __( 'disable' ) ?>
						</label>
						<small id="disableCheckBox" class="form-text text-muted"><?php echo __( 'disable-form-tip' ) ?></small>
					</div>
			
					<div class="form-check">
						<input class="form-check-input" type="checkbox" value="1" name="delete" id="deleteCheckBox" >
						<label class="form-check-label" for="deleteCheckBox">
							<?php echo __( 'delete' ) ?>
						</label>
						<small id="deleteCheckBox" class="form-text text-muted"><?php echo __( 'delete-form-tip' ) ?></small>
					</div>
				</div>
				
				<div class="tab-pane fade" id="notifications" role="tabpanel" aria-labelledby="notifications-tab">
					
					<div class="form-check">
						<input id="enableNotifications" class="form-check-input" type="checkbox" value="1" name="enable-notifications" <?php echo ( ( isset( $Form['data']['enable_notifications'] ) && $Form['data']['enable_notifications'] ) ? 'checked' : '' ) ?> />
						<label class="form-check-label" for="enableNotifications">
							<?php echo __( 'enable-notifications' ) ?>
						</label>
						<small id="enableNotificationsHelp" class="form-text text-muted"><?php echo __( 'enable-notifications-tip' ) ?></small>
					</div>
					
					<div id="notificationsFormGroup" class="<?php echo ( ( isset( $Form['data']['enable_notifications'] ) && $Form['data']['enable_notifications'] ) ? '' : 'd-none' ) ?>">
					
						<div class="form-group">
							<label for="formEmailAddress"><?php echo __( 'send-to-email-address' ) ?></label>
							<input class="form-control" type="text" id="formEmailAddress" name="email-address" value="<?php echo ( isset( $Form['data']['email_address'] ) ? $Form['data']['email_address'] : '' ) ?>">
							<small id="formEmailAddressHelp" class="form-text text-muted"><?php echo __( 'send-to-email-address-tip' ) ?></small>
						</div>
						
						<div class="form-group">
							<label for="formEmailSubject"><?php echo __( 'email-subject-line' ) ?></label>
							<input class="form-control" type="text" id="formEmailSubject" name="email-subject" value="<?php echo ( isset( $Form['data']['email_subject'] ) ? $Form['data']['email_subject'] : '' ) ?>">
						</div>
						
						<div class="form-group">
							<label for="formEmailFromName"><?php echo __( 'from-name' ) ?></label>
							<input class="form-control" type="text" id="formEmailFromName" name="from-name" value="<?php echo ( isset( $Form['data']['from_name'] ) ? $Form['data']['from_name'] : '' ) ?>">
						</div>
						
						<div class="form-group">
							<label for="formEmailFromEmail"><?php echo __( 'from-email' ) ?></label>
							<input class="form-control" type="text" id="formEmailFromEmail" name="from-email" value="<?php echo ( isset( $Form['data']['from_email'] ) ? $Form['data']['from_email'] : '' ) ?>">
							<small id="formEmailFromEmailHelp" class="form-text text-muted"><?php echo __( 'from-email-tip' ) ?></small>
						</div>
						
						<div class="form-group">
							<label for="formEmailMessage"><?php echo __( 'email-message' ) ?></label>
							<textarea class="form-control" id="formEmailMessage" rows="3" name="email-message"><?php echo ( isset( $Form['data']['email_message'] ) ? $Form['data']['email_message'] : '' ) ?></textarea>
						</div>
						
						<div id="sendRulesOptionDiv" class="row mb-3">
							<div class="col-sm-4">
								<div class="form-group">
									<label><?php echo __( 'send-this-notification-if' ) ?></label>
									<select id="sendNotificationIf" name="sendNotificationIf" class="form-control">
										<option value=""><?php echo __( 'select-field' ) ?>...</option>
										<option value="email" <?php echo ( ( isset( $Form['data']['send_notification_if'] ) && ( $Form['data']['send_notification_if'] == 'email' ) ) ? 'selected' : '' ) ?>><?php echo __( 'email' ) ?></option>
										<option value="comment" <?php echo ( ( isset( $Form['data']['send_notification_if'] ) && ( $Form['data']['send_notification_if'] == 'comment' ) ) ? 'selected' : '' ) ?>><?php echo __( 'comment-or-message' ) ?></option>
									</select>
								</div>
							</div>
						
							<div class="col-sm-3">
								<div class="form-group">
									<label>&nbsp;</label>
									<select name="sendNotificationOption" id="sendNotificationOption" class="form-control">
									<?php foreach( $emailNotificationsGroup as $_id => $row ) : ?>
										<option value="<?php echo $_id ?>" <?php echo ( ( isset( $Form['data']['send_notification_option'] ) && ( $Form['data']['send_notification_option'] == $_id ) ) ? 'selected' : '' ) ?>><?php echo $row['title'] ?></option>
									<?php endforeach ?>
									</select>
								</div>
							</div>
							
							<div class="col-sm-5">
								<div class="form-group">
									<label>&nbsp;</label>
									<input class="form-control" type="text" id="sendValueName" name="sendNotificationValue" value="<?php echo ( isset( $Form['data']['send_notification_value'] ) ? $Form['data']['send_notification_value'] : '' ) ?>" <?php echo ( ( isset( $Form['data']['send_notification_option'] ) && ( ( $Form['data']['send_notification_option'] == 'empty' ) || ( $Form['data']['send_notification_option'] == 'not-empty' ) ) ) ? 'disabled' : '' ) ?>>
								</div>
							</div>
							
						</div>
						
						<div id="dontSendRulesOptionDiv" class="row mb-3">
							<div class="col-sm-5">
								<div class="form-group">
									<label><?php echo __( 'dont-send-this-notification-if' ) ?></label>
									<select id="dontSendNotificationIf" name="dontSendNotificationIf" class="form-control">
										<option value=""><?php echo __( 'select-field' ) ?>...</option>
										<option value="email" <?php echo ( ( isset( $Form['data']['dont_send_notification_if'] ) && ( $Form['data']['dont_send_notification_if'] == 'email' ) ) ? 'selected' : '' ) ?>><?php echo __( 'email' ) ?></option>
										<option value="comment" <?php echo ( ( isset( $Form['data']['dont_send_notification_if'] ) && ( $Form['data']['dont_send_notification_if'] == 'comment' ) ) ? 'selected' : '' ) ?>><?php echo __( 'comment-or-message' ) ?></option>
									</select>
								</div>
							</div>
						
							<div class="col-sm-3">
								<div class="form-group">
									<label>&nbsp;</label>
									<select name="dontSendNotificationOption" id="dontSendNotificationOption" class="form-control">
									<?php foreach( $emailNotificationsGroup as $_id => $row ) : ?>
										<option value="<?php echo $_id ?>" <?php echo ( ( isset( $Form['data']['dont_send_notification_option'] ) && ( $Form['data']['dont_send_notification_option'] == $_id ) ) ? 'selected' : '' ) ?>><?php echo $row['title'] ?></option>
									<?php endforeach ?>
									</select>
								</div>
							</div>
							
							<div class="col-sm-4">
								<div class="form-group">
									<label>&nbsp;</label>
									<input class="form-control" type="text" id="dontSendValueName" name="dontSendNotificationValue" value="<?php echo ( isset( $Form['data']['dont_send_notification_value'] ) ? $Form['data']['dont_send_notification_value'] : '' ) ?>" <?php echo ( ( isset( $Form['data']['dont_send_notification_option'] ) && ( ( $Form['data']['dont_send_notification_option'] == 'empty' ) || ( $Form['data']['dont_send_notification_option'] == 'not-empty' ) ) ) ? 'disabled' : '' ) ?>>
								</div>
							</div>
							
						</div>
						
					</div>

				</div>
				
				<div class="tab-pane fade" id="confirmations" role="tabpanel" aria-labelledby="confirmations-tab">
					
					<div class="form-group">
						<label><?php echo __( 'confirmation-type' ) ?></label>
						<select id="confirmationType" name="confirmationType" class="form-control">
							<option value="message" <?php echo ( ( isset( $Form['data']['confirmation_type'] ) && ( $Form['data']['confirmation_type'] == 'message' ) ) ? 'selected' : '' ) ?>><?php echo __( 'message' ) ?></option>
							<option value="page" <?php echo ( ( isset( $Form['data']['confirmation_type'] ) && ( $Form['data']['confirmation_type'] == 'page' ) ) ? 'selected' : '' ) ?>><?php echo __( 'show-page' ) ?></option>
							<option value="url" <?php echo ( ( isset( $Form['data']['confirmation_type'] ) && ( $Form['data']['confirmation_type'] == 'url' ) ) ? 'selected' : '' ) ?>><?php echo __( 'go-to-url-redirect' ) ?></option>
						</select>
					</div>
					
					<div id="confirmationMessageDiv" class="mb-3 <?php echo ( ( !isset( $Form['data']['confirmation_type'] ) || ( isset( $Form['data']['confirmation_type'] ) && ( ( $Form['data']['confirmation_type'] == 'message' ) ) ) ) ? '' : 'd-none' ) ?>">
						<div class="form-group">
							<label for="confirmationMessage"><?php echo __( 'confirmation-message' ) ?></label>
							<textarea class="form-control" id="confirmationMessage" rows="3" name="confirmationMessage"><?php echo ( isset( $Form['data']['confirmation_message'] ) ? $Form['data']['confirmation_message'] : '' ) ?></textarea>
						</div>
					</div>
					
					<div id="confirmationUrlDiv" class="mb-3 <?php echo ( ( isset( $Form['data']['confirmation_type'] ) && ( $Form['data']['confirmation_type'] == 'url' ) ) ? '' : 'd-none' ) ?>">
						<div class="form-group">
							<label for="confirmationUrl"><?php echo __( 'confirmation-redirect-url' ) ?></label>
							<input class="form-control" type="text" id="confirmationUrl" name="confirmationUrl" value="<?php echo ( isset( $Form['data']['confirmation_url'] ) ? $Form['data']['confirmation_url'] : '' ) ?>">
						</div>
					</div>

					<div id="confirmationPageDiv" class="mb-3 <?php echo ( ( isset( $Form['data']['confirmation_type'] ) && ( $Form['data']['confirmation_type'] == 'page' ) ) ? '' : 'd-none' ) ?>">
						<div class="form-group">
							<label for="confirmationPage"><?php echo __( 'confirmation-page' ) ?></label>
							<select id="confirmationPage" style="width: 100%; height:36px;" name="confirmationPage" class="select2">
							<?php if ( isset( $Form['data']['confirmation_type'] ) && ( $Form['data']['confirmation_type'] == 'page' ) && !empty( $Form['data']['confirmation_page'] ) ) : ?>
							
							<option value="<?php echo $Form['data']['confirmation_page']['id'] ?>"><?php echo $Form['data']['confirmation_page']['title'] ?></option>
							
							<?php endif ?>
							</select>
						</div>
					</div>
					
				</div>
				
				
				<div class="tab-pane fade" id="template" role="tabpanel" aria-labelledby="template-tab">
				
				<?php if ( !isset( $Form['data']['saved_as_template'] ) || ( isset( $Form['data']['saved_as_template'] ) && !$Form['data']['saved_as_template'] ) ) : ?>
					
					<div class="form-check">
						<input id="saveFormTemplate" class="form-check-input" type="checkbox" value="1" name="save-template" />
						<label class="form-check-label" for="saveFormTemplate">
							<?php echo __( 'save-this-form-to-use-as-a-template' ) ?>
						</label>
						<small id="saveFormTemplateHelp" class="form-text text-muted"><?php echo __( 'save-form-as-template-tip' ) ?></small>
					</div>
					
					<div class="form-group">
						<label for="formTemplateName"><?php echo __( 'name' ) ?></label>
						<input class="form-control" type="text" id="formTemplateName" name="formTemplateName" value="">
						<small id="formTemplateNameHelp" class="form-text text-muted"><?php echo __( 'enter-the-name-of-your-template-tip' ) ?></small>
					</div>
				<?php else : ?>
					<div class="form-check">
						<input id="deleteFormTemplate" class="form-check-input" type="checkbox" value="1" name="delete-template"/>
						<label class="form-check-label" for="deleteFormTemplate">
							<?php echo __( 'delete' ) ?>
						</label>
						<small id="deleteFormTemplateHelp" class="form-text text-muted"><?php echo sprintf( __( 'delete-form-template-tip' ), $Form['data']['template_name'] ) ?></small>
					</div>
					
					<input type="hidden" name="saved-template" value="<?php echo $Form['data']['template_id'] ?>">
				<?php endif ?>
				</div>
				
			</div>
		</div>
	</div>
	
	
	
	
	
	
	<!--
		<div class="card card-default">
			<div class="card-header">
				<h3 class="card-title">
					<?php echo __( 'elements' ) ?>
				</h3>
			</div>
			
			<div class="card-body">
				<div class="row">
				<?php if ( !empty( $elementsLeft ) ) : ?>
					<div class="col-sm-6">
					<?php foreach ( $elementsLeft as $w => $elem ) : ?>
					<div class="card" id="<?php echo $w ?>">
						<div class="card-header border-transparent">
							<h4 class="card-title"><?php echo $elem['title'] ?></h4>
							<div class="card-tools">
								<div class="btn-group">
									<button type="button" id="addElementButton" data-id="<?php echo $w ?>" class="btn btn-tool addElementButton">
										<i class="fas fa-plus"></i>
									</button>
								</div>
							</div>
						  </div>
					</div>
					<?php endforeach ?>
					</div>
				<?php endif ?>
				<?php if ( !empty( $elementsRight ) ) : ?>
					<div class="col-sm-6">
						<?php foreach ( $elementsRight as $w => $elem ) : ?>
						<div class="card" data-id="<?php echo $w ?>">
							<div class="card-header border-transparent">
								<h4 class="card-title"><?php echo $widget['title'] ?></h4>
								<div class="card-tools">
									<div class="btn-group">
									<button type="button" id="addElementButton" data-id="<?php echo $w ?>" class="btn btn-tool addElementButton">
										<i class="fas fa-plus"></i>
									</button>
								</div>
								</div>
							  </div>
						</div>
					<?php endforeach ?>
					</div>
					<?php endif ?>

				</div>
			</div>
		</div>-->
		
		
		<!--
			<div class="card card card-primary card-outline">
				<div class="card-header">
					<h3 class="card-title">
						<?php echo __( 'form-options' ) ?>
					</h3>
					
					<div class="card-tools">
						<button type="button" id="minimize" class="btn btn-tool" data-card-widget="collapse">
							<i class="fas fa-plus"></i>
						</button>
					</div>
				</div>

				<div class="card-body collapse">
					
					<div class="form-group">
						<label for="formName"><?php echo __( 'name' ) ?></label>
						<input class="form-control" type="text" id="formName" name="title" value="<?php echo $Form['name'] ?>">
					</div>
					
					<div class="form-group">
						<label for="inputFrontpagePage"><?php echo __( 'membergroups' ) ?></label>
						<select  name="membergroups[]" class="form-control select2 form-select shadow-none mt-3" multiple id="slcAmp" >
							<?php $groups = AdminGroups( $Admin->GetSite(), false );
								if ( !empty( $groups ) ) :
									foreach( $groups as $group ) : ?>
									<option  value="<?php echo $group['id_group'] ?>" <?php echo ( ( !empty( $Form['groups'] ) && in_array( $group['id_group'], $Form['groups'] ) ) ? 'selected' : '' ) ?>><?php echo $group['group_name'] ?></option>
								<?php endforeach ?>
							<?php endif ?>
						</select>
						<small id="membergroupsHelp" class="form-text text-muted"><?php echo __( 'select-form-membergroup-tip' ) ?></small>
					</div>
					
					<div class="form-group">
						<label for="formCss"><?php echo __( 'form-css-class' ) ?></label>
						<input class="form-control" type="text" id="formCss" name="form-css" value="<?php echo $Form['data']['form_css'] ?>">
						<small id="formCssHelp" class="form-text text-muted"><?php echo __( 'form-css-class-tip' ) ?></small>
					</div>
					
					<div class="form-check">
						<input id="enableAntiSpamBox" class="form-check-input" type="checkbox" value="1" name="anti-spam" <?php echo ( $Form['data']['anti_spam'] ? 'checked' : '' ) ?> />
						<label class="form-check-label" for="enableAntiSpamBox">
							<?php echo __( 'enable-anti-spam-protection' ) ?>
						</label>
						<small id="enableAntiSpamBoxHelp" class="form-text text-muted"><?php echo __( 'enable-anti-spam-protection-tip' ) ?></small>
					</div>
					
					<hr />
					
					<div class="form-check">
						<input id="disableCheckBox" class="form-check-input" type="checkbox" value="1" name="disable" <?php echo ( ( $Form['disabled'] == 1 ) ? 'checked' : '' ) ?> />
						<label class="form-check-label" for="disableCheckBox">
							<?php echo __( 'disable' ) ?>
						</label>
						<small id="disableCheckBox" class="form-text text-muted"><?php echo __( 'disable-form-tip' ) ?></small>
					</div>
			
					<div class="form-check">
						<input class="form-check-input" type="checkbox" value="1" name="delete" id="deleteCheckBox" >
						<label class="form-check-label" for="deleteCheckBox">
							<?php echo __( 'delete' ) ?>
						</label>
						<small id="deleteCheckBox" class="form-text text-muted"><?php echo __( 'delete-form-tip' ) ?></small>
					</div>

				</div>
				
			</div>-->
		<!--
			<div class="card card card-primary card-outline">
				<div class="card-header">
					<h3 class="card-title">
						<?php echo __( 'notifications' ) ?>
					</h3>
					
					<div class="card-tools">
						<button type="button" id="minimize" class="btn btn-tool" data-card-widget="collapse">
							<i class="fas fa-plus"></i>
						</button>
					</div>
				</div>

				<div class="card-body collapse">

					<div class="form-check">
						<input id="enableNotifications" class="form-check-input" type="checkbox" value="1" name="enable-notifications" <?php echo ( ( isset( $Form['data']['enable_notifications'] ) && $Form['data']['enable_notifications'] ) ? 'checked' : '' ) ?> />
						<label class="form-check-label" for="enableNotifications">
							<?php echo __( 'enable-notifications' ) ?>
						</label>
						<small id="enableNotificationsHelp" class="form-text text-muted"><?php echo __( 'enable-notifications-tip' ) ?></small>
					</div>
					
					<div id="notificationsFormGroup" class="<?php echo ( ( isset( $Form['data']['enable_notifications'] ) && $Form['data']['enable_notifications'] ) ? '' : 'd-none' ) ?>">
					
						<div class="form-group">
							<label for="formCss"><?php echo __( 'form-css-class' ) ?></label>
							<input class="form-control" type="text" id="formCss" name="form-css" value="<?php echo $Form['data']['form_css'] ?>">
							<small id="formCssHelp" class="form-text text-muted"><?php echo __( 'form-css-class-tip' ) ?></small>
						</div>
					</div>

				</div>
				
			</div>-->

			<div class="card card-default">
				<div class="card-header">
					<h3 class="card-title">
						<?php echo __( 'preview' ) ?>
					</h3>
				</div>

				<div class="card-body" id="demoForm">
					<?php FormElementToHtml( $Form['elements'], true, true ) ?>
				</div>
			</div>

	</div>
		
	<div class="col-md-6">
		
		<div class="<?php echo ( !empty( $Form['elements'] ) ? 'd-none' : '' ) ?>" id="emptyFormAlert">
			<div class="alert alert-info" role="alert" >
				<?php echo __( 'add-a-field-from-the-left-to-this-area' ) ?>
			</div>
		</div>

		<div class="card h-300">
				<div class="card-header">
					<h3 class="card-title"><?php echo __( 'form-builder' ) ?></h3>
				</div>
				<div class="card-body">
					<section id="formBuilder" class="connectedSortable">
					<?php if ( !empty( $Form['elements'] ) ) : ?>
					
						<?php foreach( $Form['elements'] as $elmnt ) : ?>
							<div data-id="<?php echo $elmnt['id'] ?>" id="form-item-<?php echo $elmnt['id'] ?>" class="card collapsed-card">
								<div class="card-header bg-light">
									<h3 class="card-title">
										<?php echo __( $elmnt['elementId'] ) ?>
									</h3>
									<div class="card-tools">
										<button type="button" id="minimize" class="btn btn-tool" data-card-widget="collapse">
											<i class="fas fa-plus"></i>
										</button>
										<button type="button" id="close" data-id="<?php echo $elmnt['id'] ?>" class="btn btn-tool remElementButton">
											<i class="fas fa-times"></i>
										</button>
									</div>
								</div>
								<div class="card-body">
									<?php if ( !empty( $elmnt['data'] ) ) : ?>
										<?php BuildFormElementHtml( $elmnt['data'], $elmnt['elementId'], $elmnt['id'], null, true ) ?>
									<?php endif ?>
								</div>
							</div>
						<?php endforeach ?>
					
					<?php endif ?>
					</section>
				</div>
			</div>
	</div>
	
		<input type="hidden" name="_token" value="<?php echo generate_token( 'edit_form_' . $Form['id'] ) ?>">
		
		<div class="align-middle">
			<div class="float-left mt-1">
				<button type="submit" class="btn btn-primary btn-sm" name="save"><?php echo $L['save'] ?></button>
				<a class="btn btn-secondary btn-sm" href="<?php echo $Admin->GetUrl( 'forms' ) ?>" role="button"><?php echo $L['cancel'] ?></a>
			</div>
		</div>

	</form>
</div>