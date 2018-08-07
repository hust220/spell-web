function JSCalc()
{
	var body = document.getElementsByTagName("body")[0];
	var div = document.createElement("div");
	var input = document.createElement("input");

	div.style.position = "absolute";
	div.style.top = "50%";
	div.style.left = "50%";
	div.style.width = "300px";
	div.style.height = "60px";
	div.style.margin = "-50px 0 0 -170px";
	div.style.border = "3px double #999999";
	div.style.padding = "20px";
	div.style.opacity = "0.85";
	div.style.backgroundColor = "#CCCCCC";
	div.style.fontSize = "60px";
	div.style.lineHeight = "60px";
	div.style.textAlign = "right";

	body.appendChild(div);

	input.type = "text";
	input.style.position = "absolute";
	input.style.top = "50%";
	input.style.left = "50%";
	input.style.width = "300px";
	input.style.margin = "-30px 0 0 -150px";
	input.style.border = "1px solid #CCCCCC";
	input.style.backgroundColor = "#CCCCCC";
	input.style.fontSize = "60px";
	input.style.fontFamily = "Arial, Helvetica, sans-serif";
	input.style.color = "#FFFFFF";
	input.style.textAlign = "right";

	input = div.appendChild(input);

	verticalAlign(input, 60);

	input.focus();

	input.onkeypress = function(e)
	{
		var allowed = false;
		var allowedKeys = [8,13,32,37,39,40,41,42,43,45,46,47,48,49,50,51,52,53,54,55,56,57,106,107,109,111,120,190,191];
		var controlKeys = [8,13,32,37,39,40,41,42,43,45,47,106,107,109,111,120,190,191];

		if (!e)
		{
			 e = event;
		}

		for (var i = 0; i < allowedKeys.length; i++)
		{
			if (e.keyCode == allowedKeys[i] || e.charCode == allowedKeys[i])
			{
				allowed = true;

				break;
			}
		}

		if (allowed)
		{
			if (this.calculated)
			{
				allowed = false;

				for (var i = 0; i < controlKeys.length; i++)
				{
					if (e.keyCode == controlKeys[i] || e.charCode == controlKeys[i])
					{
						allowed = true;
					}
				}

				if (!allowed)
				{
					this.value = "";
					verticalAlign(this, 60);
				}
			}

			this.calculated = false;

			if (e.keyCode == "13" || e.charCode == "13")
			{
				var calculated = this.value;

				calculated = calculated.replace(/x/g, "*");
				calculated = eval(calculated);

				if (calculated != null)
				{
					calculated = calculated.toString();

					if (calculated.length < 9)
					{
						verticalAlign(this, 60);
					}
					else
					{
						if (calculated.length > 9)
						{
							calculated = calculated.substring(0, 14);
						}

						verticalAlign(this, 30);
					}

					this.value = calculated;
					this.calculated = true;
				}
			}
			else if (e.keyCode != 8 && e.keyCode != "13")
			{
				if (this.value.length == 8)
				{
					verticalAlign(this, 30);
				}
				else if (this.value.length > 15)
				{
					return false;
				}
			}
			else if (this.value.length == 9)
			{
				verticalAlign(this, 60);
			}
		}
		else
		{
			return false;
		}

		return true;
	}

	input.onblur = function(e)
	{
		opacityDown(this.parentNode);
	}
};




function opacityDown(theElement)
{
	var opacity = parseFloat(theElement.style.opacity);

	if (opacity < 0.08)
	{
		theElement.parentNode.removeChild(theElement);
	}
	else
	{
		opacity -= 0.07;
		theElement.style.opacity = opacity;
		setTimeout(function(){opacityDown(theElement);}, 50);
	}
	
	return true;
};




function verticalAlign(theElement, pixelSize)
{
	theElement.parentNode.style.top = document.documentElement.scrollTop + window.innerHeight/2 + "px";
	theElement.style.fontSize = pixelSize + "px";
	theElement.style.lineHeight = pixelSize + "px";
	
	var height = theElement.clientHeight;

	theElement.style.marginTop = -parseInt(height / 2) + "px";

	return true;
};

