<div class="mb-3">
	<div class="form-group "><span class="charcounter" id="titleNum"></span>
		<label class="form-label required" for="postTitle"><?php echo $L['title'] ?></label>
		<input type="text" id="postTitle" name="title" onkeyup="countChar(this, 120, '#titleNum');" class="form-control mb-4" placeholder="<?php echo $L['enter-title'] ?>" value="<?php echo $Post->Title() ?>">
	</div>
</div>

<div class="mb-3">
	<div class="form-group required">
		<label class="col-sm-2 control-label" for="input-meta-title1">Meta Tag Title</label>
		<input type="text" name="product_description[1][meta_title]" value="Apple Cinema 30" placeholder="Meta Tag Title" id="input-meta-title1" class="form-control" />
	</div>
</div>