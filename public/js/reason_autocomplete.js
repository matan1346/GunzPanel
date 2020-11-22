$(function () {
	$("#reason_form").dialog({
		autoOpen: false,
		draggable: false,
		resizable: false,
		height: 115,
		width: 300,
		modal: true,
		close: function () {
			$("#reason_form_url").val("");
			$("#reason_form_type1").val("");
			$("#reason_form_type2").val("");
			$("#reason_form_rs").val("");
			$("#reason_form_reas").val("");
			$("#reason_form_error").css("display", "none");
		},
		open: function () {
			setTimeout('$("#reason_form_reas").focus();', 401);
		},
		show: "explode",
		hide: "explode",
	});

	$(".reason_form_button").button();
	$("#reason_form_button_send").click(function () {
		if($("#reason_form_reas").val().length > 2) {
			printResultsN($("#reason_form_url").val(), $("#reason_form_type1").val(), $("#reason_form_type2").val(), $("#reason_form_rs").val(), $("#reason_form_reas").val());
			$("#reason_form").dialog("close");
		} else {
			$("#reason_form_error_txt").html("Reason length must be bigger than 2");
			$("#reason_form_error").fadeIn(500);

			setTimeout('$("#reason_form_error").fadeOut(200);', 2000);
		}
	});
	$("#reason_form_button_cancle").click(function () {
		$("#reason_form").dialog("close");
	});

	$("#reason_form").bind("keypress", function(key) {
		if(key.keyCode == 13) {
			$("#reason_form_button_send").click();
		}
	});

	$("button").each(function () {
		var myBt = $(this);
		$(this).parent().keypress(function (e) {
			if(e.keyCode == 13) {
				myBt.click();
			}
		});
	});

// ------------------------------------------------------------Auto Complete -----------------


	jQuery.fn.customComplete = function () {
		var autoCompleteClass = "autocomplete";

		var par = $(this).parents(".form");
		var sel = par.find(".ac_select");
		var inp = $(this);

		var offsetFixWidth = 2;
		var offsetFixHeight = 6;

		var relX = $(this).offset().left - par.offset().left;
		var relY = $(this).offset().top - par.offset().top;

		relY += $(this).height() + offsetFixHeight;
		var inpW = $(this).width() + offsetFixWidth;

		$(this).focus(function () {
			if(!par.find("." + autoCompleteClass).hasClass(autoCompleteClass)) {
				par.append('<div class="' + autoCompleteClass + '" style="position: absolute; top: ' + relY + 'px; left: ' + relX + 'px; width: ' + inpW + 'px;"></div>');
			}

			$(this).attr("selindex", -1);
			$(this).attr("completecount", 0);
		});

		$(this).blur(function () {
			if(!par.find(".autocomplete-data-selected").hasClass("autocomplete-data-selected")) {
				$("." + autoCompleteClass).remove();
			}
		});

		$(this).keyup(function (event) {
			if($(this).val().length == 0 || event.keyCode == 38 || event.keyCode == 40) {
				return true;
			} else {
				$(this).attr("firsttype", $(this).val());
			}
			

			var req = $.get("ajax/uget.php", {"uid": $(this).val(), "type": sel.val() }, function (data) {
				var resObj = JSON.parse(data);

				var dataDiv = par.find("." + autoCompleteClass);
				if(dataDiv.hasClass(autoCompleteClass)) {
					inp.attr("completecount", resObj.res.length);

					var addToClass = "";
					for(i=0; i<resObj.res.length; i++) {
						addToClass += '<div id="autocomplete-data-' + i + '" class="autocomplete-data" index="' + i + '">' + resObj.res[i] + '</div>';
					}
					$("." + autoCompleteClass).html(addToClass);

					$(".autocomplete-data").hover(function () {
						$(".autocomplete-data").removeClass("autocomplete-data-selected");
						inp.attr("selindex", parseInt($(this).attr("index")));
						$(this).addClass("autocomplete-data-selected");
					}, function () {
						$(this).removeClass("autocomplete-data-selected");
					});

					$(".autocomplete-data").click(function () {
						inp.val($(this).html());
						inp.focus();
					});
				}
			});
		});

		$(this).keydown(function (event) {
			if(event.keyCode == 9 || event.keyCode == 13) {
				$("." + autoCompleteClass).remove();
				return true;
			}

			var selIndex = parseInt($(this).attr("selindex"));

			if(par.find("." + autoCompleteClass).hasClass(autoCompleteClass)) {
				if(event.keyCode == 38) {
					if(selIndex > -1) {
						selIndex--;
					} else {
						selIndex = parseInt($(this).attr("completecount"))-1;
					}
				} else if(event.keyCode == 40) {
					if(selIndex < parseInt($(this).attr("completecount"))-1) {
						selIndex++;
					} else {
						selIndex = -1;
					}
				}

				if(event.keyCode == 38 || event.keyCode == 40) {
					$(this).attr("selindex", selIndex);

					$(".autocomplete-data").removeClass("autocomplete-data-selected");

					if(selIndex == -1) {
						$(this).val($(this).attr("firsttype"));
					} else {
						$("#autocomplete-data-"+selIndex).addClass("autocomplete-data-selected");
						$(this).val($("#autocomplete-data-"+selIndex).html());
					}
					return false;
				}
			}
		});
	}

	$(".ac_input").each(function () {
		$(this).customComplete();
	});

});