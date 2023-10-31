<?php defined('TOKICMS') or die('Hacking attempt...');

class Editor
{
	private $html = '';
	private $code = '';
	private $temp = '';
	private $content;
	private $fired = [];
	private $id;
	private $editor;
	private $editorId;
	private $mainEditor;
	private $editorData;
	private $draftsData;
	private $js;
	private $userId;
	private $timeCalled = 0;
	private $site;
	private $lang;
	private $blog;
	public 	$rows;
	public 	$height;
	public 	$name;
	public 	$addExtraValues;
	
	public function __construct()
	{
		global $Admin;

		$this->editor 			= $Admin->Settings()::Get()['html_editor'];
		$this->userId 			= $Admin->UserID();
		$this->site 			= $Admin->GetSite();
		$this->lang 			= $Admin->GetLang();
		$this->blog 			= $Admin->GetBlog();
		$this->name 			= 'content';
		$this->addExtraValues 	= true;
		
		$this->editorData 	= Json( $Admin->Settings()::Get()['editor_data'] );
		$this->draftsData 	= Json( $Admin->Settings()::Get()['drafts_data'] );
	}
	
	private function AddOneTimeCode()
	{
		$this->timeCalled++;
		
		global $Admin;
		
		if ( $this->timeCalled == 1 )
		{
			//Set the generic code
			$this->GenericCode();
			
			//Add eny extra code
			$this->ExtraCode();
			
			//Add the drafts data
			$this->Drafts();
			
			//Add the modal HTML
			$this->LinksModal();

			//Insert the generic code into footer
			$Admin->AddFooterCode( $this->temp );
		}
	}
	
	public function Init( $content = null, $height = '600px', $id = 'editor', $main = true, $rows = 10 ) 
	{
		global $Admin;
		
		$this->AddOneTimeCode();
		
		//Clean any previous code
		$this->code = '';

		$this->content 		= $content;
		$this->id 			= $id;
		$this->height 		= $height;
		$this->rows 		= $rows;
		$this->mainEditor 	= $main;
		$this->editorId 	= ( ( $this->id == 'mainEditor' ) ? 'main' : 'price' );
		
		//Set the html code
		$this->SetEditor();

		//Enable the footer code
		$Admin->AddFooterCode( $this->code );
		
		return $this->html;
	}
	
	private function GenericCode()
	{
		$html = '';
		
		if ( $this->editor == 'simplemde' )
		{
			$html .= '<script src="' . TOOLS_HTML . 'easy-markdown-editor/src/js/easymde.js"></script>' . PHP_EOL;
		}

		$html .= '<style>.modal-body {position: relative;overflow-y: auto;min-height: 100px !important;		  max-height: 600px !important;}.categories-list {max-height: 100px;overflow-y: auto;justify-content: space-between;}.categories-list li {align-items: center;justify-content: center;}</style>' . PHP_EOL;
		
		$this->temp .= $html;
	}

	private function LinksModal()
	{
		if ( !$this->addExtraValues )
		{
			return;
		}
		
		global $Admin;
		
		$html = '';
		
		$html .= '
		<!-- Modal for Links -->
		<div class="modal fade" id="addLink" role="dialog" aria-hidden="true">
			<div class="modal-dialog modal centered" role="document" style="padding-right: 20px; display: block; width:100%;">
			<!-- Modal content-->
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="modal-title">' . __( 'insert-link' ) . '</h4>

						<ul class="nav nav-tabs' . ( $Admin->Settings()::IsTrue( 'enable_link_manager' ) ? '' : ' d-none' ) . '" id="custom-tabs-one-tab" role="tablist">
							<li class="nav-item">
								<a class="nav-link active" id="normal-links-tab" data-toggle="pill" href="#normal-links" role="tab" aria-controls="normal-links" aria-selected="true">' . __( 'normal-link' ) . '</a>
							</li>';

		if ( $Admin->Settings()::IsTrue( 'enable_link_manager' ) )
		{
			$linkSettings = $Admin->Settings()::ShortLinksSettings();

			if ( !empty( $linkSettings ) && $linkSettings['enable'] )
			{
				$html .= '
							<li class="nav-item">
								<a class="nav-link" id="short-link-tab" data-toggle="pill" href="#short-links" role="tab" aria-controls="short-link" aria-selected="false">' . __( 'short-link' ) . '</a>
							</li>';
			}

		}

		$html .= '
						</ul>
					</div>
						
					<div class="modal-body">
						<div class="tab-content" id="custom-tabs-one-tabContent">
							<div class="tab-pane fade show active" id="normal-links" role="tabpanel" aria-labelledby="normal-links-tab">
								<label for="destinationUrl" class="form-label">' . __( 'enter-the-destination-url' ) .'</label>
									
								<div class="mb-3 row">
									<label for="inputUrl" class="col-sm-2 col-form-label">' . __( 'url' ) .'</label>
									<div class="col-sm-10">
										<input type="text" class="form-control" id="inputUrl" name="url" value="">
									</div>
								</div>
									
								<div class="mb-3 row">
									<label for="inputText" class="col-sm-2 col-form-label">' . __( 'link-text' ) .'</label>
									<div class="col-sm-10">
										<input type="text" class="form-control" id="inputText" name="text" value="">
									</div>
								</div>
										
								<div class="form-check">
									<input class="form-check-input" type="checkbox" value="" id="openNewTabLink">
									<label class="form-check-label" for="openNewTabLink">
										' . __( 'open-link-in-new-tab' ) . '
									</label>
								</div>
										
								<div class="form-check">
									<input class="form-check-input" type="checkbox" value="" id="addNoFollow">
									<label class="form-check-label" for="addNoFollow">
										' . __( 'add-rel-nofollow-to-link' ) . '
									</label>
								</div>
										
								<div class="form-check">
									<input class="form-check-input" type="checkbox" value="" id="addSponsored">
									<label class="form-check-label" for="addSponsored">
										' . __( 'add-rel-sponsored-to-link' ) . '
									</label>
								</div>
									
								<hr />
										
								<div class="form-check">
									<input class="form-check-input" type="checkbox" value="" id="deepSearch">
									<label class="form-check-label" for="deepSearch">
										' . __( 'search-in-all-langs-sites' ) . '
									</label>
								</div>
									
								<div class="mb-3">
									<label for="searchContent" class="form-label">' . __( 'search-existing-content' ) . '</label>
									<input type="text" class="form-control" id="searchContent" name="search" value="">
								</div>
									
								<div class="query-notice" id="query-notice-message" style="display: none;">
									<em class="query-notice-default">' . __( 'no-search-term-specified' ) . ' ' . __( 'showing-recent-items' ) . '</em>
									<em class="query-notice-hint screen-reader-text">' . __( 'search-or-select-an-item' ) . '</em>
								</div>
									
								<div class="search-waiting" id="search-waiting" style="display: none;">
									<span class="spinner"><img src="' . HTML_ADMIN_PATH_THEME . 'assets/img/loading.gif"></span>
								</div>

								<div id="search-results" class="query-results" tabindex="0" style="display: none;"></div>
									
								<div id="latest-posts" class="latest-posts" tabindex="0" style="display: none;"></div>
									
								<div class="modal-footer">
									<div class="text-left"><button type="button" class="btn btn-default" data-dismiss="modal">' . __( 'cancel' ) . '</button></div>
									<div class="text-right"><button id="insertLink" type="button" class="btn btn-primary" data-dismiss="modal">' . __( 'add-link' ) . '</button></div>
								</div>
							</div>';
									
		if ( $Admin->Settings()::IsTrue( 'enable_link_manager' ) )
		{
			$linkSettings = $Admin->Settings()::ShortLinksSettings();

			if ( !empty( $linkSettings ) && $linkSettings['enable'] )
			{
				$html .= '
						<div class="tab-pane fade" id="short-links" role="tabpanel" aria-labelledby="short-links-tab">
							<div class="row">
								<div class="col-5 col-sm-3">
									<div class="nav flex-column nav-tabs h-100" id="vert-tabs-tab" role="tablist" aria-orientation="vertical">
										<a class="nav-link active" id="vert-tabs-home-tab" data-toggle="pill" href="#vert-tabs-home" role="tab" aria-controls="vert-tabs-home" aria-selected="true">Home</a>
										<a class="nav-link" id="vert-tabs-profile-tab" data-toggle="pill" href="#vert-tabs-profile" role="tab" aria-controls="vert-tabs-profile" aria-selected="false">Profile</a>
									</div>
								</div>
								
								<div class="col-7 col-sm-9">
									<div class="tab-content" id="vert-tabs-tabContent">
										<div class="tab-pane text-left fade show active" id="vert-tabs-home" role="tabpanel" aria-labelledby="vert-tabs-home-tab">
											dddd
										</div>
										
										<div class="tab-pane fade" id="vert-tabs-profile" role="tabpanel" aria-labelledby="vert-tabs-profile-tab">
											Mauris tincidunt mi at erat gravida, eget tristique urna bibendum. Mauris pharetra purus ut ligula tempor, et vulputate metus facilisis. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Maecenas sollicitudin, nisi a luctus interdum, nisl ligula placerat mi, quis posuere purus ligula eu lectus. Donec nunc tellus, elementum sit amet ultricies at, posuere nec nunc. Nunc euismod pellentesque diam.
										</div>
									</div>
								</div>
							</div>
						</div>';
			}

		}
		
		$html .= '	<input type="hidden" id="editorID" value="">
					</div>
				</div>
			</div>
		</div>' . PHP_EOL;
		
		$this->temp .= $html;
	}
	
	private function ExtraCode()
	{		
		global $Admin;
		
		$html = '';

		$html .= '<script type="application/javascript">' . PHP_EOL;
		
		$html .= 'var fired = 0;';
		
		if ( !$this->addExtraValues )
		{
			$html .= '</script>' . PHP_EOL;

			$this->temp .= $html;
			return;
		}

		$html .= '
		function fileManagerOpen() {
			$("#addImage").modal("show");
		}' . PHP_EOL;

		$html .= '
		function dragUploadFile(file) {
			$("#progressBar").removeClass("d-none");
			$("#progressBar").children(".progress-bar").width("0");
	
			// Data to send via AJAX
			var formData = new FormData();
			formData.append("file", file);
			formData.append("id", "' . Router::GetVariable( 'key' ) . '");
			formData.append("site", "' . $this->site . '");
			formData.append("lang", "' . $this->lang . '");
	
			$.ajax(
			{
				url: "' . AJAX_ADMIN_PATH . 'drop-media-upload/",
				type: "POST",
				data: formData,
				cache: false,
				contentType: false,
				processData: false,
				xhr: function() {
					var xhr = $.ajaxSettings.xhr();
					if (xhr.upload) {
						xhr.upload.addEventListener("progress", function(e) {
							if (e.lengthComputable) {
								var percentComplete = (e.loaded / e.total) * 100;
								$("#progressBar").children(".progress-bar").width(percentComplete + "%");
							}
						}, false);
					}
					return xhr;
				}
			}).done(function(response) {
				if (response.status == 0) {
					// Progress bar
					$("#progressBar").addClass("d-none");
					fileManagerOpen();
				} else {
					$("#progressBar").children(".progress-bar").addClass("bg-danger");
					//Alert the user about the error
					alert("File Manager. " + response.message);
				}
			});
		}</script>' . PHP_EOL;

		$html .= '<script type="application/javascript">' . PHP_EOL;
		
		$html .= '
		$(document).ready(function() {
			$("#filesToUpload").on("change", function(e) {
				var filesToUpload = $("#filesToUpload")[0].files;
				for (var i = 0; i < filesToUpload.length; i++) {
					dragUploadFile(filesToUpload[i]);
				}
			});
	
			$(window).on("dragover dragenter", function(e) {
				e.preventDefault();
				e.stopPropagation();
			});

			$(window).on("drop", function(e) {
				e.preventDefault();
				e.stopPropagation();
		
				$("#filesToUpload").prop("files", e.originalEvent.dataTransfer.files);
				$("#filesToUpload").trigger("change");
		
		
			});
		});' . PHP_EOL;
		
		$html .= '
		function ClearValues()
		{
			$("#inputUrl").val("");
			$("#inputText").val("");
			$("#searchContent").val("");
			//$("#editorID").val("");
			$("#deepSearch").prop("checked", false);
			$("#addSponsored").prop("checked", false);
			$("#openNewTabLink").prop("checked", false);
			$("#addNoFollow").prop("checked", false);
		}' . PHP_EOL;
		
		$html .= '
		function NothingFound()
		{
			var nothing = "<p>' . __( 'nothing-found' ) . '</p>";
			$("#search-results").hide();
			$("#latest-posts").show();
			$("#latest-posts").html(nothing);
		}' . PHP_EOL;

		$html .= '
		function escapeString(string)
		{
			return string.replace(/&/g, \'&amp;\').replace(/>/g, \'&gt;\').replace(/</g, \'&lt;\').replace(/"/g, \'&quot;\').replace(/\'/g, \'&quot;\');
		}' . PHP_EOL;
		
		$html .= '
		function ShowHtmlPosts(data, where)
		{
			if ( data == "" )
			{
				NothingFound();
				return;
			}
			
			var htmlData = "";
			
			htmlData += "<ul class=\"list-group categories-list\">";

			$.each(data, function(i, item) {

				htmlData += "<li class=\"list-item\" data-url=\""+item["postURL"]+"\" data-id=\""+item["id"]+"\"><a href=\"javascript: void(0);\" id=\""+item["id"]+"\" class=\"das\">"+item["title"]+"</a>";
				
				if ( item["postType"] == "page" )
				{
					htmlData += " <em>Page</em>";
				}
					
				htmlData += " <span class=\"text-secondary float-right\">["+item["time"]+"]</span></li>";
			});

			htmlData += "</ul>";
			
			//htmlData += "<nav><ul class=\"pagination\"><li class=\"page-item\"><a class=\"page-link\" href=\"#\">Previous</a></li><li //class=\"page-item\"><a class=\"page-link\" href=\"#\">Next</a></li></ul></nav>";
			
			if ( where == "latest" )
			{
				$("#search-results").hide();
				$("#latest-posts").show();
				$("#latest-posts").html(htmlData);
			}
			else
			{
				$("#latest-posts").hide();
				$("#search-results").show();
				$("#search-results").html(htmlData);
			}

			CheckLinks();
		}' . PHP_EOL;
		
		$html .= '
		function ShowLatest(deepSearch, search, siteId, blogId, langId)
		{
			var where = "latest";
			$("#search-waiting").hide();
			$("#query-notice-message").show();
			
			$.ajax(
			{
				url: "' . AJAX_ADMIN_PATH . 'search-posts/",
				type: "POST",
				data: {deepSearch:deepSearch,search:search,site:siteId,blog:blogId,lang:langId},
				dataType: "json",
				complete: function() {
					//
				},
				success: function(json) {			
					if (json["error"]) {
						NothingFound();
					}

					if ( json["data"] )
					{
						ShowHtmlPosts(json["data"], where);
					}
				},
				error: function(xhr, ajaxOptions, thrownError) {
					alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				}
			});
		}' . PHP_EOL;
		
		$html .= '
		function SearchPosts (value, deep, site, lang, blog) {
			var where = "search";
			$("#search-waiting").show();
			$("#query-notice-message").hide();
			//var deepSearch = $("#deepSearch").is(":checked");
			
			$.ajax(
			{
				url: "' . AJAX_ADMIN_PATH . 'search-posts/",
				type: "POST",
				data: {deepSearch:deep,search:value,site:site,blog:blog,lang:lang},
				dataType: "json",
				cache: false
			})
			.done(function(json)
			{
				$("#search-waiting").hide();
				if (json["error"]) {
					NothingFound();
				}

				if ( json["data"] )
				{
					ShowHtmlPosts(json["data"], where);
				}
			});
		};' . PHP_EOL;
		
		$html .= '
		function ShowPosts() {
			var typingTimer;
			var typingInterval = 500;

			var siteId = "' . $this->site . '";
			var langId = "' . $this->lang . '";
			var blogId = "' . $this->blog . '";
			
			ShowLatest(false, null, siteId, blogId, langId);

			$("#searchContent").on("input", function(e) {
				var val = this.value;
				var deep = $("#deepSearch").is(":checked");
				clearTimeout(typingTimer);
				typingTimer = setTimeout(SearchPosts, typingInterval, val, deep, siteId, langId, blogId);
			});
				
			$("#searchContent").on("keydown", function(e) {
				clearTimeout(typingTimer);
			});
		};' . PHP_EOL;
		
		$html .= '
		function CheckLinks() {
			$( "li.list-item" ).click(function() {    
				var a = $(this).attr("data-url");
				var b = $(this).find("a").text();
				//var b = $(this).text();
				
				var inputTxt = $("#inputText").val();
				$("#inputUrl").val(a);
				
				if ( inputTxt === "" )
				{
					$("#inputText").val(b);
				}
			});
		}' . PHP_EOL;
		
		$html .= '</script>' . PHP_EOL;

		$html .= '<script type="application/javascript">' . PHP_EOL;
		
		$html .= '
		$(document).ready(function()
		{
			$(".shortcodeButton").click(function (e)
			{
				e.preventDefault();
				
				var id = $(this).data("key");
				var description = $(this).data("description");
				
				id = id.trim();
				
				if ( id === "form" )
				{
					$("#addNewFormInEditor").modal("show");
					
					$("#addNewFormButton").click(function(e) {
						e.preventDefault();
						
						var formId = $("#newFormSelection").val();
						
						var htmlcode = "[form id=\""+formId+"\"]";
						
						editorReplaceContent(htmlcode,"main");
						
						$("#addNewFormInEditor").modal("hide");
					});
				}
				
				else if ( id === "google-map" )
				{
					$("#addGMapInEditor").modal("show");
					
					$("#addGMapButton").click(function(e) {
						e.preventDefault();

						var googleMapWidth = $("#googleMapWidth").val();
						var googleMapHeight = $("#googleMapHeight").val();
						var googleMapMarker = $("#googleMapMarker").val();
						var googleMapZoom = $("#googleMapZoom").val();
						var googleMapTitle = $("#googleMapTitle").val();
						var googleMapCss = $("#googleMapCss").val();
						
						var htmlcode = "[g-map width=\""+googleMapWidth+"\" height=\""+googleMapHeight+"\" marker=\""+googleMapMarker+"\" zoom=\""+googleMapZoom+"\" title=\""+googleMapTitle+"\" css=\""+googleMapCss+"\"]";
						
						editorReplaceContent(htmlcode,"main");
						
						$("#addGMapInEditor").modal("hide");
					});
				}
				
				else if ( id === "contact-form" )
				{
					var htmlcode = "[contact-form]";
					
					editorReplaceContent(htmlcode,"main");
				}
				
				else if ( id === "price-list" )
				{
					$("#postPriceList").val(null).trigger("change");
					$("#from-this-post").attr("checked", false);
					
					$("#addPriceListInEditor").modal("show");
					
					$("#addPriceListButton").click(function(e) {
						e.preventDefault();
						
						var postId = $("#postPriceList option:selected").val();
						var thisPost = $("#from-this-post").is(":checked");
						var thisPostId = $("#thisPostId").val();
						
						var id = ( thisPost ? thisPostId : postId );

						var htmlcode = "[price-list id=\""+id+"\"]";
						
						editorReplaceContent(htmlcode,"main");
						
						$("#addPriceListInEditor").modal("hide");
					});
				}
				
				else if ( id === "single-price" )
				{
					$("#singlePrice").val(null).trigger("change");

					$("#addSinglePriceInEditor").modal("show");
					
					$("#addSinglePriceButton").click(function(e) {
						e.preventDefault();
						
						var priceId = $("#singlePrice option:selected").val();

						var htmlcode = "[price id=\""+priceId+"\"]";
						
						editorReplaceContent(htmlcode,"main");
						
						$("#addSinglePriceInEditor").modal("hide");
					});
				}
				
				else if ( id === "best-price" )
				{
					$("#bestPrice").val(null).trigger("change");

					$("#addBestPriceInEditor").modal("show");
					
					$("#addBestPriceButton").click(function(e) {
						e.preventDefault();
						
						var postId = $("#bestPrice option:selected").val();

						var htmlcode = "[best-price id=\""+postId+"\"]";
						
						editorReplaceContent(htmlcode,"main");
						
						$("#addBestPriceInEditor").modal("hide");
					});
				}
				
				else if ( id === "interlink-post" )
				{
					$("#postInterlink").val(null).trigger("change");
					$("#add-interlink-description").attr("checked", false);
					$("#add-interlink-prices").attr("checked", false);
					
					$("#addNewInterlinkInEditor").modal("show");
					
					$("#addNewInterlinkButton").click(function(e) {
						e.preventDefault();
						
						var postId = $("#postInterlink option:selected").val();
						var linkTarget = $("#linkTarget option:selected").val();
						var descr = $("#add-interlink-description").is(":checked");
						var prices = $("#add-interlink-prices").is(":checked");
						
						var htmlcode = "[interlink id=\""+postId+"\" target=\""+linkTarget+"\" descr=\""+descr+"\" prices=\""+prices+"\"]";
						
						editorReplaceContent(htmlcode,"main");
						
						$("#addNewInterlinkInEditor").modal("hide");
					});
				}
				else
				{
					alert(id);
				}
			});
			
			$("#insertLink").click(function (e)
			{
				e.preventDefault();
				var html = "";
				var inputUrl = $("#inputUrl").val();
				var inputTxt = $("#inputText").val();
				var openNewTab = false;
				var noFollow = false;
				var sponsored = false;
				var eId = $("#editorID").val();
				
				eId = eId.trim();
				
				if ( inputUrl != "" )
				{
					html += "<a href=\""+inputUrl+"\"";
					
					if ( $("#openNewTabLink").is(":checked") || $("#addNoFollow").is(":checked") || $("#addSponsored").is(":checked") )
					{
						if ( $("#openNewTabLink").is(":checked") )
						{
							html += " target=\"_blank\"";
							openNewTab = true;
						}
						
						if ( $("#addNoFollow").is(":checked") || $("#addSponsored").is(":checked") || openNewTab )
						{
							html += " rel=\"";
							
							if ( $("#addNoFollow").is(":checked") )
							{
								html += "nofollow";
								noFollow = true;
							}
							
							if ( $("#addSponsored").is(":checked") && !$("#addNoFollow").is(":checked" ) )
							{
								html += "sponsored";
								sponsored = true;
							}
							
							if ( $("#addSponsored").is(":checked") && $("#addNoFollow").is(":checked") )
							{
								html += " sponsored";
								sponsored = true;
							}
							
							if ( openNewTab )
							{
								if ( !noFollow && !sponsored )
								{
									html += "noopener";
								}
								else
								{
									html += " noopener";
								}
							}
							
							html += "\"";
						}
					}
					
					html += ">"+inputTxt+"</a>";
				}
				
				editorReplaceContent(html, eId);
				
				$("#addLink").removeClass("in");
				$("#addLink").modal("hide");
			});
		});</script>' . PHP_EOL;

		$this->temp .= $html;
	}
	
	private function EditorJs()
	{
		global $Post;
		
		$html = '';
		
		$blocks = ( ( !$Post ) ? array() : ( !empty( $Post->Blocks() ) ? $Post->Blocks() : ConvertToBlocks( $Post->Content() ) ) );

		$html .= '
			<script src="' . TOOLS_HTML . 'editor-js/editor.js"></script>' . PHP_EOL;
		
		$html .= '
			<script src="' . TOOLS_HTML . 'editor-js/libs.js"></script>' . PHP_EOL;
			
		$html .= '
		<script type="application/javascript">
		const editorJSConfig = {};
		class test {
			static get toolbox() {
				return {
				  title: "Image",
				  icon: \'<svg width="17" height="15" viewBox="0 0 336 276" xmlns="http://www.w3.org/2000/svg"><path d="M291 150V79c0-19-15-34-34-34H79c-19 0-34 15-34 34v42l67-44 81 72 56-29 42 30zm0 52l-43-30-56 30-81-67-66 39v23c0 19 15 34 34 34h178c17 0 31-13 34-29zM79 0h178c44 0 79 35 79 79v118c0 44-35 79-79 79H79c-44 0-79-35-79-79V79C0 35 35 0 79 0z"/></svg>\'
				};
			}
			render(){
				return document.createElement("input");
			}

			save(blockContent){
				return {
					url: blockContent.value
				}
			}
		}
		
		const editor = new EditorJS(
		{
			onReady: () => {
				new Undo({ editor });
				new DragDrop(editor);
			},
			holder: "' . $this->id . '",
			autofocus: true,
			data: {
			},
			tools: {
				raw: RawTool,
				code: CodeTool,
				embed: {
					class: Embed,
					inlineToolbar: true
				},
				underline: Underline,
				image: SimpleImage,
				//image: {
				//		class: ImageTool,
				//		config: {
				//			uploader: {
				//				uploadByUrl(url){
				//					return MyAjax.upload(file).then(() => {
				//						return {
				//							success: 1,
				//							file: {
				//							  url: \'https://codex.so/upload/redactor_images/o_e48549d1855c7fc1807308dd14990126.jpg\',
				//							}
				//						}
				//					})
				//				}
				//			}
				//		}
				//	},
					twoColumns: {
					  class: EditorJSLayout.LayoutBlockTool,
					  config: {
						EditorJS,
						editorJSConfig,
						enableLayoutEditing: false,
						enableLayoutSaving: false,
						initialData: {
						  itemContent: {
							1: {
							  blocks: [],
							},
							2: {
							  blocks: [],
							},
						  },
						  layout: {
							type: "container",
							id: "",
							className: "",
							style:
							  "border: 1px solid #000000; display: flex; justify-content: space-around; padding: 16px; ",
							children: [
							  {
								type: "item",
								id: "",
								className: "",
								style: "border: 1px solid #000000; padding: 8px; ",
								itemContentId: "1",
							  },
							  {
								type: "item",
								id: "",
								className: "",
								style: "border: 1px solid #000000; padding: 8px; ",
								itemContentId: "2",
							  },
							],
						  },
						},
					  },
					  shortcut: "CMD+2",
					  toolbox: {
						icon: `
						  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 512 512">
							<rect x="128" y="128" width="336" height="336" rx="57" ry="57" fill="none" stroke="currentColor" stroke-linejoin="round" stroke-width="32"/>
							<path d="M383.5 128l.5-24a56.16 56.16 0 00-56-56H112a64.19 64.19 0 00-64 64v216a56.16 56.16 0 0056 56h24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32"/>
						  </svg>
						`,
						title: "2 columns",
					  },
					},
					paragraph: {
						class: Paragraph,
						inlineToolbar: true,
					},
					markdown: {
						class: MarkDown,
						inlineToolbar: false,
					},
					inlineCode: InlineCode,
					style: EditorJSStyle.StyleInlineTool,
					warning: Warning,
					header: {
					  class: Header,
					  inlineToolbar: ["link"]
					},
					list: {
						class: List,
						inlineToolbar: true,
						config: {
							defaultStyle: "ordered"
						}
					},
					delimiter: Delimiter,
					table: {
					  class: Table,
					  inlineToolbar: true,
					  config: {
						rows: 2,
						cols: 3,
						}
					},
					checklist: {
						class: Checklist,
						inlineToolbar: true,
					},
					linkTool: {
						class: LinkTool,
						config: {
							endpoint: "' . AJAX_ADMIN_PATH . 'fetch-url/",
						}
					},
					quote: {
						class: Quote,
						inlineToolbar: true,
						config: {
							quotePlaceholder: "Enter a quote",
							captionPlaceholder: "Quote\'s author",
						},
					}
				},
				onReady: function(){
					//saveButton.click();
				},
				onChange: (api, event) => {
					confirmChange = true;
					editor.save().then((outputData) => {
						var id = "' . Router::GetVariable( 'key' ) . '";
						var user = "' . $this->userId . '";
						$.ajax({
							url: "' . AJAX_ADMIN_PATH . 'save-blocs/",
							type: "post",
							dataType: "json",
							data: {post:id,user:user,data:outputData},
							success: function(json) {
								if (json["error"]) {
									//alert(json["error"]);
									console.log("Saving failed: ", json["error"])
								}

								if (json["success"]) {
									console.log("Data Saved")
								}
							},
							error: function(xhr, ajaxOptions, thrownError) {
								alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
							}
						});
					//console.log("Article data: ", outputData)
					}).catch((error) => {
						console.log("Saving failed: ", error)
					});
				},
				data: {
					blocks: ' . json_encode( $blocks ) . '
				}
			})

			editor.isReady
			  .then(() => {
				console.log("Editor.js is ready to work!")
			  })
			  .catch((reason) => {
				console.log(`Editor.js initialization failed because of ${reason}`)
			  });
			  
		function editorInsertContent(type, content) {
			editor.blocks.insert(type, content);
		}
		</script>';
		
		return $html;
	}
	
	private function SimpleJs()
	{
		$html = '
		<script type="application/javascript">
			function editorGetContent() {
				return $("#' . $this->id . '").val();
			}
			
			// Insert HTML content at the cursor position
			function editorInsertContent(html)
			{
				var textarea = document.getElementById("' . $this->id . '");
				var value = editorGetContent();
	 
				var len = textarea.value.length;
				var start = textarea.selectionStart;
				var end = textarea.selectionEnd;
				var sel = textarea.value.substring(start, end);
	 
				// This is the selected text and alert it
				alert(sel);
				
				//Nothing selected
				if ( sel == "" )
				{
					textarea.value = value + html + "\n";
				}
				else
				{
					var replace = "<b>" + sel + "<b>" + "\n";
	 
					// Here we are replacing the selected text with this one
					textarea.value = textarea.value.substring(0,start) + replace + textarea.value.substring(end,len);
				}
				
				//if ( startPosition == startPosition )
				//{
					//$("#' . $this->id . '").val(html);
				//}
				//else
				//{
					//$("' . $this->id . '").replaceSelectedText(html);
				//}
			}
			
			function removeParag(id) {
				var content = editorGetContent("' . $this->id . '");
				var contentEdited = content.replace(/(<p[^>]+?>|<p>|<\/p>)/img, "");
				$("#id").val(contentEdited);
			}
			
			removeParag("' . $this->id . '");
		</script>' . PHP_EOL;
		
		return $html;
	}
	
	private function SimpleMDE()
	{		
		$html = '
		<script type="application/javascript">
			//var editorId = "' . $this->editorId . '";
			var editorId = "";
			
			const ' . $this->id . ' = new EasyMDE({
				element: document.getElementById("' . $this->id . '"),
				status: false,
				promptURLs: true,
				toolbarTips: false,
				toolbarGuideIcon: true,
				autofocus: false,
				placeholder: "",
				lineWrapping: false,
				autoDownloadFontAwesome: false,
				indentWithTabs: false,
				lineWrapping: true,
				tabSize: 2,
				maxHeight: "' . $this->height . '",
				spellChecker: ' . ( ( !isset( $this->editorData['spell-checker'] ) || empty( $this->editorData['spell-checker'] ) ) ? "false" : $this->editorData['spell-checker'] ) . ',
				toolbar: [' . $this->editorData['toolbar'];
				
				if ( $this->addExtraValues )
					$html .= ',
					"|",
					{
						name: "custom",
						action: function addLink(){
							var addLink = new bootstrap.Modal(document.getElementById(\'addLink\'), {
								keyboard: false
							});
							
							editorId = ( "' . $this->id . '" === "mainEditor" ) ? "main" : "price";
							
							$("#editorID").val(editorId);
							
							ClearValues();
							addLink.show();
							var curr = ' . $this->id . '.codemirror.getSelection();
							if ( curr !== "" )
							{
								$("#inputText").val(curr);
							}
							
							ShowPosts();
						},
						className: "fa fa-link",
						title: "' . __( 'add-new-link' ) . '",
					},
					{
						name: "pageBreak",
						action: function addPageBreak()
						{
							var cm = ' . $this->id . '.codemirror;
							output = "<!--more-->" + "\n";
							cm.replaceSelection(output);
						},
						className: "bi-file-earmark-break",
						title: "' . __ ( 'pagebreak' ) . '",
					}';
			
		$html .= '
				]
			});';
			
		if ( $this->addExtraValues )
		{
			$html .= '
				' . $this->id . '.codemirror.on("change", () => 
				{
					fired++;
					
					//easymde fires "change" on loading even without any change
					//this workaroung is to make sure the post has been changed before asking for confirmation
					if ( fired > 1 )
					{
						confirmChange = true;
					}
				});';
		}
		$html .= '
			editorId = ( "' . $this->id . '" === "mainEditor" ) ? "main" : "price";';
			
		if ( $this->addExtraValues )
		{
			$html .= '
			removeParag(editorId);';
		}
		
		$html .= '
		</script>' . PHP_EOL;
		
		//Don't add the same code many times
		if ( in_array( $this->editor, $this->fired ) || !$this->addExtraValues )
			return $html;		
		
		$this->code .= '
			<script type="application/javascript">
		
			function removeParag(id) {
				if( (typeof(id) == "undefined") || ( id === "" ) )
					return;

				var content = editorGetContent(editorId);
				
				var contentEdited = content.replace(/(<p[^>]+?>|<p>|<\/p>)/img, "");
				
				if ( editorId === "main" )
				{
					mainEditor.value(contentEdited);
					mainEditor.codemirror.refresh();
				}
				
				else if ( editorId === "price" )
				{
					priceDescr.value(contentEdited);
					priceDescr.codemirror.refresh();
				}
			}
			
			function editorInsertContent(content,id) {
				if( (typeof(id) == "undefined") || ( id === "" ) )
					return;
				
				if ( id === "main" )
				{
					var text = mainEditor.value();
					mainEditor.codemirror.replaceSelection(content + "\n");
					mainEditor.codemirror.refresh();
				}
				
				else if ( id === "price" )
				{
					var text = priceDescr.value();
					priceDescr.codemirror.replaceSelection(content + "\n");
					priceDescr.codemirror.refresh();
				}
			}
		
			function editorReplaceContent(html,id) {
				if( (typeof(id) == "undefined") || ( id === "" ) )
					return;
				
				if ( id === "main" )
				{
					mainEditor.codemirror.replaceSelection(html);
				}
				
				else if ( id === "price" )
				{
					priceDescr.codemirror.replaceSelection(html);
				}
				
				else
				{
					console.log("Error Replace text in: " + id);
				}
			}

			function editorGetContent(id) {
				
				if( (typeof(id) == "undefined") || ( id === "" ) )
					return;
				
				var val = "";
				
				if ( id === "main" )
				{
					val = mainEditor.value();
				}
				
				else if ( id === "price" )
				{
					val = priceDescr.value();
				}
				
				return val;
			}
			</script>' . PHP_EOL;
			
		array_push( $this->fired, $this->editor );

		return $html;
	}
	
	private function TinyMCE()
	{
		$html = '<script src="' . TOOLS_HTML . 'tinymce/tinymce.min.js?version=5.7"></script>' . PHP_EOL;
		
		$html .= '<script type="application/javascript">
			// Returns the content of the editor
			function editorGetContent() {
				return tinymce.get(\'' . $this->id . '\').getContent();
			}
				
			// Insert HTML content at the cursor position
			function editorInsertContent(html)
			{
				tinymce.activeEditor.insertContent(html);
			}
			
			var seperator = \'<!--more-->\';

			tinymce.init({
				selector: "#' . $this->id . '",
				auto_focus: "editor",
				element_format : "html",
				entity_encoding : "raw",
				skin: "oxide",
				schema: "html5",
				width: "100%",
				height : "640",
				autoresize_min_height: 400,
				autoresize_max_height: 800,
				statusbar: false,
				menubar:false,
				branding: false,
				browser_spellcheck: true,
				pagebreak_separator: seperator,
				pagebreak_split_block: true,
				paste_as_text: true,
				remove_script_host: false,
				convert_urls: true,
				relative_urls: false,
				valid_elements: "*[*]",
				cache_suffix: "?version=5.4.1",
				//document_base_url : "<?php echo SITE_URL ?>",
				plugins: ["code autolink image link pagebreak advlist lists textpattern table"],
				toolbar1: "formatselect bold italic forecolor backcolor removeformat | bullist numlist table | blockquote alignleft aligncenter alignright | link unlink image code pagebreak",
				toolbar2: "",
				language: "en",
				content_css: "' . TOOLS_HTML . 'tinymce/css/lightmode-toolbar.css",
			});
		</script>' . PHP_EOL;

		return $html;
	}
	
	private function Drafts()
	{
		if ( !$this->addExtraValues )
		{
			return;
		}
		
		$html = '<script type="application/javascript">';

		if ( !empty( $this->draftsData ) && $this->draftsData['enable_post_drafts'] && $this->draftsData['enable_auto_drafts'] )
		{
			$timeToSave = ( ( is_numeric( $this->draftsData['auto_save'] ) && ( $this->draftsData['auto_save'] >= 30 ) ) ? $this->draftsData['auto_save'] : 30 );

			$html .= '
			function saveAsDraft(id, title, content) {
				var site = "' . $this->site . '";
				var user = "' . $this->userId . '";
				var arr = [];
				
				$.ajax(
				{
					url: "' . AJAX_ADMIN_PATH . 'auto-draft/",
					type: "POST",
					data: {id:id,user:user,title:title,content:content,site:site},
					dataType: "json",
					cache: false
				})
				.done(function(data)
				{
					arr = data;
					console.log("post: " + title + " (id: " + id + ") Autosaved");
				})
				.fail(function(){
					console.log("Autosave failed");
				});
				
				return arr;
			}' . PHP_EOL;

			$html .= '
			if (typeof editorGetContent != "function") {
				window.editorGetContent = function(){
					return $("#mainEditor").val();
				};
			}' . PHP_EOL;
			
			$html .= '
			// Autosave
			var currentContent = editorGetContent("main");
			
			setInterval(function() {
				var id = ' . Router::GetVariable( 'key' ) . ';
				var title = $("#postTitle").val();
				var content = editorGetContent("main");
				
				// Autosave when content has at least 100 characters
				if (content.length<100) {
					return false;
				}
				
				// Autosave only when the user change the content
				if (currentContent!=content) {
					currentContent = content;
					
					saveAsDraft(id, title, content);
					
					//saveAsDraft(id, title, content).then(function(data) {
					//	if (data.status==0) {
					//		console.log("Autosaved");
					//	}
					//});
				}
			},1000*' . $timeToSave . ');';
		}
		else
		{
			$html .= '
			function saveAsDraft(id, title, content) {
				return null;
			}' . PHP_EOL;
		}
		
		$html .= '</script>' . PHP_EOL;

		$this->temp .= $html;
	}
	
	private function SetEditor()
	{
		if ( $this->editor == 'editor-js' )
		{
			$this->html = '<div id="' . $this->id . '"></div>';
		}
			
		elseif ( $this->editor == 'simple' )
		{
			$this->html = '<textarea id="' . $this->id . '" rows="' . $this->rows . '" class="editor form-control" name="' . $this->name . '">' . $this->content . '</textarea>';
		}
			
		else
		{
			$this->html = '<textarea id="' . $this->id . '" rows="' . $this->rows . '" name="' . $this->name . '">' . $this->content . '</textarea>';
		}
		
		//Add the necessary js code
		$this->SetEditors();
	}
	
	private function SetEditors()
	{
		$html = '';
		
		if ( $this->editor == 'editor-js' )
		{
			$html .= $this->EditorJs();
		}
		
		if ( $this->editor == 'simple' )
		{
			$html .= $this->SimpleJs();
		}
		
		if ( $this->editor == 'simplemde' )
		{
			$html .= $this->SimpleMDE();
		}
		
		if ( $this->editor == 'tinymce' )
		{
			$html .= $this->TinyMCE();
		}

		//Add the editors code
		$this->code .= $html;
	}
}