/**
 * Displays line numbers in a textbox.
 * 
 * Usage:
 *      $("id").linedtextarea();
 *      $$("selector").each(function(element) { 
 *          element.linedtextarea();
 *      });
 * 
 * Converted to Prototype.js from http://alan.blog-city.com/jquerylinedtextarea.htm
 */
(function() {
    
    /**
     * Helper function to make sure the line numbers are always
     * kept up to the current system
     * 
     * @returns current line number
     */
    var fillOutLines = function(codeLines, h, lineNo) {
        while ((codeLines.measure("height") - h) <= 0) {
            codeLines.insert("<div class='lineno' id='lineno-" + lineNo + "'>" + lineNo + "</div>");
            lineNo++;
        }
        return lineNo;
    };
    
    /**
     * Highlights the current selected row number
     */
    var selectionChange = function() {
        if (this.selectionStart !== undefined && this.selectionStart >= 0) {
            var start = this.selectionStart;
            var end = this.selectionEnd;
            var firstLine = this.value.substr(0, start).split("\n").length;
            var lastLine = firstLine + this.value.substr(start, end - start).split("\n").length;
            $$(".lineselect").each(function(elem) {
                elem.removeClassName("lineselect");
            });
            for (var line = firstLine; line < lastLine; line++) {
                $("lineno-" + line).addClassName("lineselect");
            }
        }
    };
    
    var construct = function(element) {
        /* Preserve current scroll */
        var currentScroll = element.scrollTop;
        /* Store original style to support destruction */
        element.origStyle = element.getAttribute("style");
        
        var lineNo = 1;
        /* Turn off the wrapping of as we don't want to screw up the line numbers */
        element.writeAttribute("wrap", "off");
        element.setStyle({resize: "none"});
        var originalTextAreaWidth = element.measure("border-box-width");

        /* Wrap the text area in the elements we need */
        Element.wrap(element, "div", { 'class': "linedtextarea" });
        var linedTextAreaDiv = element.up().wrap("div", {
            'class': "linedwrap",
            style: "width:" + originalTextAreaWidth + "px",
        });

        var linesDiv = new Element("div", {
            'class': "lines",
            style: "width:50px;height:" + (element.measure("height") + 6) + "px;",
        });
        var codeLinesDiv = new Element("div", { 'class': "codelines" });
        linesDiv.insert(codeLinesDiv);

        linedTextAreaDiv.insert({ top: linesDiv });

        /* Draw the number bar; filling it out where necessary */
        lineNo = fillOutLines(codeLinesDiv, linesDiv.measure("height"), lineNo);

        /* Set the width */
        var sidebarWidth = linesDiv.measure("border-box-width");
        var paddingHorizontal = linedTextAreaDiv.measure("border-left") + 
                                linedTextAreaDiv.measure("border-right") + 
                                linedTextAreaDiv.measure("padding-left") + 
                                linedTextAreaDiv.measure("padding-right");
        var textareaNewWidth = originalTextAreaWidth - sidebarWidth - paddingHorizontal - 20;

        element.setStyle({ width: textareaNewWidth + "px" });

        /* React to the scroll event */
        element.observe("scroll", function() {
            var scrollTop = element.scrollTop;
            var clientHeight = element.clientHeight;
            codeLinesDiv.setStyle({'margin-top': (-1 * scrollTop) + "px"});
            lineNo = fillOutLines(codeLinesDiv, scrollTop + clientHeight, lineNo);
        });

        /* Should the textarea get resized outside of our control */
        element.observe("resize", function() {
            linesDiv.height(element.clientHeight + 6);
        });
        
        element.observe("keyup", selectionChange);
        element.observe("click", selectionChange);
        element.observe("focus", selectionChange);
        
        element.scrollTop = currentScroll;
    };
    
    var destroy = function(element) {
        var currentScroll = element.scrollTop;
        /* Remove events */
        element.stopObserving();
        /* Reset style */
        element.setAttribute("style", element.origStyle);
        /* Move element out of our divs and destroy them */
        var linedWrap = document.getElementsByClassName("linedwrap")[0];
        linedWrap.parentNode.appendChild(element);
        linedWrap.remove();
        /* Preserve scroll */
        element.scrollTop = currentScroll;
    };
    
    var LinedTextarea = {
        linedtextarea: function(element, command) {
            element = $(element);
            if (command == "destroy") {
                destroy(element);
            } else {
                construct(element);
            }
        },
    };
    
    Element.addMethods(LinedTextarea);
})();