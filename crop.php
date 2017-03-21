<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en" xml:lang="en" xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta content="text/html; charset=UTF-8" http-equiv="Content-Type" />
        <title>jQuery Image Cropping Plug-In</title>
        <style>
			
		</style>
		<style>
			* {
				margin : 0;
				outline : 0;
				padding : 0;
			}
			 
			body {
				background-color : #ededed;
				color : #646464;
				font-family : 'Verdana', 'Geneva', sans-serif;
				font-size : 12px;
				text-shadow : 0 1px 0 #ffffff;
			}
			 
			h1 {
				font-size : 24px;
				font-weight : normal;
				margin : 0 0 10px 0;
			}
			 
			div#wrapper {
				margin : 25px 25px 25px 25px;
			}
			 
			div.image-decorator {
				-moz-border-radius : 5px 5px 5px 5px;
				-moz-box-shadow : 0 0 6px #c8c8c8;
				-webkit-border-radius : 5px 5px 5px 5px;
				-webkit-box-shadow : 0 0 6px #c8c8c8;
				background-color : #ffffff;
				border : 1px solid #c8c8c8;
				border-radius : 5px 5px 5px 5px;
				box-shadow : 0 0 6px #c8c8c8;
				display : inline-block;
				height : 360px;
				padding : 5px 5px 5px 5px;
				width : 480px;
			}
			div#image-crop-overlay {
				background-color : #ffffff;
				overflow : hidden;
			}
			 
			div#image-crop-outline {
				background : #ffffff url('outline.gif');
				overflow : hidden;
			}
		</style>
        <script src="http://code.jquery.com/jquery-2.1.4.min.js"></script>
        <script>
			// Always wrap a plug-in in '(function($) { // Plug-in goes here }) (jQuery);'
			(function($) {
				$.imageCrop = function(object, customOptions) {};
			 
				$.fn.imageCrop = function(customOptions) {
					//Iterate over each object
					this.each(function() {
						var currentObject = this,
							image = new Image();
			 
						// And attach imageCrop when the object is loaded
						image.onload = function() {
							$.imageCrop(currentObject, customOptions);
						};
			 
						// Reset the src because cached images don't fire load sometimes
						image.src = currentObject.src;
					});
			 
					// Unless the plug-in is returning an intrinsic value, always have the
					// function return the 'this' keyword to maintain chainability
					return this;
				};
			}) (jQuery);
			
			$.imageCrop = function(object, customOptions) {
			// Rather than requiring a lengthy amount of arguments, pass the
			// plug-in options in an object literal that can be extended over
			// the plug-in's defaults
			var defaultOptions = {
				allowMove : true,
				allowResize : true,
				allowSelect : true,
				minSelect : [0, 0],
				outlineOpacity : 0.5,
				overlayOpacity : 0.5,
				selectionPosition : [0, 0],
				selectionWidth : 0,
				selectionHeight : 0
			};

			// Set options to default
			var options = defaultOptions;

			// And merge them with the custom options
			setOptions(customOptions);
		};
		
		// Initialize the image layer
var $image = $(object);

// Initialize an image holder
var $holder = $('<div />')
    .css({
        position : 'relative'
    })
    .width($image.width())
    .height($image.height());

// Wrap the holder around the image
$image.wrap($holder)
    .css({
        position : 'absolute'
    });
	
// Initialize an overlay layer and place it above the image
var $overlay = $('<div id="image-crop-overlay" />')
    .css({
        opacity : options.overlayOpacity,
        position : 'absolute'
    })
    .width($image.width())
    .height($image.height())
    .insertAfter($image);

// Initialize a trigger layer and place it above the overlay layer
var $trigger = $('<div />')
    .css({
        backgroundColor : '#000000',
        opacity : 0,
        position : 'absolute'
    })
    .width($image.width())
    .height($image.height())
    .insertAfter($overlay);


// Initialize an outline layer and place it above the trigger layer
var $outline = $('<div id="image-crop-outline" />')
    .css({
        opacity : options.outlineOpacity,
        position : 'absolute'
    })
    .insertAfter($trigger);
	
// Initialize a selection layer and place it above the outline layer
var $selection = $('<div />')
    .css({
        background : 'url(' + $image.attr('src') + ') no-repeat',
        position : 'absolute'
    })
    .insertAfter($outline);


// Initialize global variables
var selectionExists,
    selectionOffset = [0, 0],
    selectionOrigin = [0, 0];

// Verify if the selection size is bigger than the minimum accepted
// and set the selection existence accordingly
if (options.selectionWidth > options.minSelect[0] &&
    options.selectionHeight > options.minSelect[1])
        selectionExists = true;
    else
        selectionExists = false;

// Call the 'updateInterface' function for the first time to
// initialize the plug-in interface
updateInterface();

if (options.allowSelect)
    // Bind an event handler to the 'mousedown' event of the trigger layer
    $trigger.mousedown(setSelection);

// Get the current offset of an element
function getElementOffset(object) {
    var offset = $(object).offset();

    return [offset.left, offset.top];
};

// Get the current mouse position relative to the image position
function getMousePosition(event) {
    var imageOffset = getElementOffset($image);

    var x = event.pageX - imageOffset[0],
        y = event.pageY - imageOffset[1];

    x = (x < 0) ? 0 : (x > $image.width()) ? $image.width() : x;
    y = (y < 0) ? 0 : (y > $image.height()) ? $image.height() : y;

    return [x, y];
};

// Update the overlay layer
function updateOverlayLayer() {
    $overlay.css({
        display : selectionExists ? 'block' : 'none'
    });
};

// Update the trigger layer
function updateTriggerLayer() {
    $trigger.css({
        cursor : options.allowSelect ? 'crosshair' : 'default'
    });
};

// Update the selection
function updateSelection() {
    // Update the outline layer
    $outline.css({
        cursor : 'default',
        display : selectionExists ? 'block' : 'none',
        left : options.selectionPosition[0],
        top : options.selectionPosition[1]
    })
    .width(options.selectionWidth)
    .height(options.selectionHeight);

    // Update the selection layer
    $selection.css({
        backgroundPosition : ( - options.selectionPosition[0] - 1) + 'px ' + ( - options.selectionPosition[1] - 1) + 'px',
        cursor : options.allowMove ? 'move' : 'default',
        display : selectionExists ? 'block' : 'none',
        left : options.selectionPosition[0] + 1,
        top : options.selectionPosition[1] + 1
    })
    .width((options.selectionWidth - 2 > 0) ? (options.selectionWidth - 2) : 0)
    .height((options.selectionHeight - 2 > 0) ? (options.selectionHeight - 2) : 0);
};

// Update the cursor type
function updateCursor(cursorType) {
    $trigger.css({
            cursor : cursorType
        });

    $outline.css({
            cursor : cursorType
        });

    $selection.css({
            cursor : cursorType
        });
};

// Update the plug-in's interface
function updateInterface(sender) {
    switch (sender) {
        case 'setSelection' :
            updateOverlayLayer();
            updateSelection();

            break;
        case 'resizeSelection' :
            updateSelection();
            updateCursor('crosshair');

            break;
        default :
            updateTriggerLayer();
            updateOverlayLayer();
            updateSelection();
    }
};

// Set a new selection
function setSelection(event) {
    // Prevent the default action of the event
    event.preventDefault();

    // Prevent the event from being notified
    event.stopPropagation();

    // Bind an event handler to the 'mousemove' and 'mouseup' events
    $(document).mousemove(resizeSelection).mouseup(releaseSelection);

    // Notify that a selection exists
    selectionExists = true;

    // Reset the selection size
    options.selectionWidth = 0;
    options.selectionHeight = 0;

    // Get the selection origin
    selectionOrigin = getMousePosition(event);

    // And set its position
    options.selectionPosition[0] = selectionOrigin[0];
    options.selectionPosition[1] = selectionOrigin[1];

    // Update only the needed elements of the plug-in interface
    // by specifying the sender of the current call
    updateInterface('setSelection');
};

// Resize the current selection
function resizeSelection(event) {
    // Prevent the default action of the event
    event.preventDefault();

    // Prevent the event from being notified
    event.stopPropagation();

    var mousePosition = getMousePosition(event);

    // Get the selection size
    options.selectionWidth = mousePosition[0] - selectionOrigin[0];
    options.selectionHeight = mousePosition[1] - selectionOrigin[1];

    if (options.selectionWidth < 0) {
        options.selectionWidth = Math.abs(options.selectionWidth);
        options.selectionPosition[0] = selectionOrigin[0] - options.selectionWidth;
    } else
        options.selectionPosition[0] = selectionOrigin[0];

    if (options.selectionHeight < 0) {
        options.selectionHeight = Math.abs(options.selectionHeight);
        options.selectionPosition[1] = selectionOrigin[1] - options.selectionHeight;
    } else
        options.selectionPosition[1] = selectionOrigin[1];

    // Update only the needed elements of the plug-in interface
    // by specifying the sender of the current call
    updateInterface('resizeSelection');
};

// Release the current selection
function releaseSelection(event) {
    // Prevent the default action of the event
    event.preventDefault();

    // Prevent the event from being notified
    event.stopPropagation();

    // Unbind the event handler to the 'mousemove' event
    $(document).unbind('mousemove');

    // Unbind the event handler to the 'mouseup' event
    $(document).unbind('mouseup');

    // Update the selection origin
    selectionOrigin[0] = options.selectionPosition[0];
    selectionOrigin[1] = options.selectionPosition[1];

    // Verify if the selection size is bigger than the minimum accepted
    // and set the selection existence accordingly
    if (options.selectionWidth > options.minSelect[0] &&
        options.selectionHeight > options.minSelect[1])
        selectionExists = true;
    else
        selectionExists = false;

    // Update only the needed elements of the plug-in interface
    // by specifying the sender of the current call
    updateInterface('releaseSelection');
};


$(document).ready(function() {
    $('img#example').imageCrop({
        overlayOpacity : 0.25
    });
});
		</script>
    </head>
 
    <body>
        <div id="wrapper">
            <h1>jQuery Image Cropping Plug-In</h1>
 
            <div class="image-decorator">
                <img alt="jQuery Image Cropping Plug-In" height="360" id="example" src="resources/images/example.jpg" width="480" />
            </div><!-- .image-decorator -->
        </div><!-- #wrapper -->
    </body>
</html>