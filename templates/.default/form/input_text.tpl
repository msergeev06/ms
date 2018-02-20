<div class="form-group">
	<label for="#FIELD_NAME#" class="col-sm-2 control-label">#FIELD_TITLE#</label>
	<div class="col-sm-10">
		#INPUT_TEXT#
		<span class="help-block small" id="#FIELD_NAME#-help">#FIELD_HELP#</span>
		<span style="color: red;" class="help-block" id="#FIELD_NAME#-error"></span>
	</div>
</div>
<script type="text/javascript">
	$("##FIELD_NAME#").on("focusout",function(){
		var input = $("##FIELD_NAME#");
		var parent = input.parent();
		var err = $("##FIELD_NAME#-error");

		if (#FIELD_REQUIRED#0)
		{
			if (input.val()==""){
				parent.removeClass("has-success");
				parent.addClass("has-error");
				err.text("#ERROR_TEXT_FIELD_REQUIRED#");
				return;
			}
		}

		checkValue(input,parent,err,"#NAMESPACE#","#FUNCTION#","#PATH#");
	});
</script>