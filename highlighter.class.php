<?php
include_once("keywords.php");
class highlighter {
	private $fileName;
	private $fileExtension;
	private $insideParenthesisColor;
	private $insideBracketColor;
	
	public function __construct() {
		$this->fileName = "";
		$this->fileExtension = "";
		//Color Configuration
		$this->insideParenthesisColor = "iPC";
		$this->insideBracketColor = "iBC";
	}
	
	public function applycolor($fileLocation = "") {
		if($fileLocation == "")
		{	return;	}
		else 
		{
			if(file_exists($fileLocation)) {
				$temp = explode("/",$fileLocation);
				$this->fileName = trim(end($temp));
				$temp = explode(".",$this->fileName);
				$this->fileExtension = trim(end($temp));
				$fileContent = trim(file_get_contents($fileLocation, true));
				$fileContent = htmlentities($fileContent,ENT_NOQUOTES);
				if($fileContent == "")
				{	return;	}
			}
			else
			{	return; }
		}
		
		$blockFound = 0;
		$blockFoundColor = array();
		$parenthesisFound = 0;
		$bracketFound = 0;
		
		$line = 1;
		$characterBuffer = "";
		$lastCharacter = "";
		$counter = 0;
		$contentSize = strlen($fileContent);

		$outputContent ="<font class='lin'>".$line."</font> ";
		while($counter < $contentSize) {
			$character = $fileContent[$counter];
			$code = intval(ord($character));
			if(($code >= 97 && $code <= 122) || ($code >= 65 && $code <= 90))
			{	$characterBuffer .= $character;	}
			else
			{
				if($code == 9) {
					$outputContent .= "    ";
				}
				else if($code == 10)	{	//Find EOL (End of Line)
					if($characterBuffer != "") {
						$outputContent .= $this->checker($characterBuffer);
						$characterBuffer = "";
					}
					$line++;
					if($blockFound == 0)
					{	$outputContent .= $character."<font class='lin'>".$line."</font> ";	}
					else
					{	$outputContent .= "</font>".$character."<font class='lin'>".$line."</font> <font class='".$blockFoundColor[$blockFound-1]."'>";	}
				}
				else if($code == 32) {	//Find Space
					if($characterBuffer != "") {	
						$outputContent .= $this->checker($characterBuffer);
						$characterBuffer = "";
					}
					$outputContent .= $character;
				}
				else if($character == "\"" || $character == "'") {	//Find Quotes
					if($characterBuffer != "") {
						$outputContent .= $this->checker($characterBuffer);	
						$characterBuffer = "";
					}
					$outputContent .= "<font class='qC'>".$character;
					$foundCharacter = $character;
					$counter++;
					while($counter < $contentSize) {
						$character = $fileContent[$counter];
						$code = intval(ord($character));
						if($code == 9) {
							$outputContent .= "    ";
						}
						else if($character == $foundCharacter) {
							$outputContent .= $character;
							if($lastCharacter == "\\") {
								$lastCharacter = "";
							}
							else
							{	break;	}
						}
						else if($character == "\\" && $lastCharacter == "\\") {
							$outputContent .= $character;
							$lastCharacter = "";
						}
						else
						{
							$lastCharacter = $character;
							$code = intval(ord($character));
							if($code != 10) 
							{	$outputContent .= $character;	}
							else
							{
								$line++;
								$outputContent .= "</font>".$character."<font class='lin'>".$line."</font>	<font class='qC'>";
							}
						}
						$counter++;
					}
					$outputContent .= "</font>";
				}
				else if($character == "(" || $character == ")") {	//Find Parenthesis
					if($characterBuffer != "") {
						$outputContent .= $this->checker($characterBuffer);	
						$characterBuffer = "";
					}
					if($parenthesisFound == 0) {
						$outputContent .= "<font class='pC'>".$character."</font><font class='iPC'>";
						$parenthesisFound++;
						$blockFoundColor[$blockFound] = $this->insideParenthesisColor;
						$blockFound++;
					}
					else
					{
						if($character == "(") 
						{	$parenthesisFound++;	}
						if($character == ")") 
						{	$parenthesisFound--;	}
						if($parenthesisFound == 0) {
							$outputContent .= "</font><font class='pC'>".$character."</font>";
							$blockFound--;
							unset($blockFoundColor[$blockFound]);
						}
						else
						{	$outputContent .= $character;	}
					}
				}
				else if($character == "[" || $character == "]")	{	//Find Bracket
					if($characterBuffer != "") {
						$outputContent .= $this->checker($characterBuffer);	
						$characterBuffer = "";
					}
					if($bracketFound == 0) {
						$outputContent .= "<font class='bC'>".$character."</font><font class='iBC'>";
						$bracketFound++;
						$blockFoundColor[$blockFound] = $this->insideBracketColor;
						$blockFound++;
					}
					else
					{
						if($character == "[") 
						{	$bracketFound++;	}
						if($character == "]") 
						{	$bracketFound--;	}
						if($bracketFound == 0) {
							$outputContent .= "</font><font class='bC'>".$character."</font>";
							$blockFound--;
							unset($blockFoundColor[$blockFound]);
						}
						else
						{	$outputContent .= $character;	}
					}
				}
				else if($character == "/" && (isset($fileContent[$counter+1]) && ($fileContent[$counter+1] == "*" || $fileContent[$counter+1] == "/"))) {	//Find Comment
					if($characterBuffer != "") {
						$outputContent .= $this->checker($characterBuffer);	
						$characterBuffer = "";
					}
					$outputContent .= "<font class='cC'>".$fileContent[$counter].$fileContent[$counter+1];
					if($fileContent[$counter+1] == "*") {
						$counter += 2;
						while($counter < $contentSize) {
							$character = $fileContent[$counter];
							$code = intval(ord($character));
							if($code == 9) {
								$outputContent .= "    ";
							}
							else if($code != 10) {
								if($character == "*" && (isset($fileContent[$counter+1]) && ($fileContent[$counter+1] == "/"))) {
									$counter++;
									$outputContent .= $character.$fileContent[$counter]."</font>";
									break;
								}
								else
								{	$outputContent .= $character;	}
							}
							else
							{
								$line++;
								$outputContent .= "</font>".$character."<font class='lin'>".$line."</font> <font class='cC'>";
							}
							$counter++;
						}
					}
					else
					{
						$counter += 2;
						while($counter < $contentSize) {
							$character = $fileContent[$counter];
							$code = intval(ord($character));
							if($code == 10) {
								$outputContent .= "</font>";
								$counter--;
								break;
							}
							$outputContent .= $character;
							$counter++;
						}
					}
				}
				else if($characterBuffer != "") {
					$outputContent .= $this->checker($characterBuffer).$character;
					$characterBuffer = "";
				}
				else
				{	$outputContent .= $character;	}
			}
			$counter++;
		}
		$rerurnData = "<div class='fN'>".$this->fileName."</div>";
		$rerurnData .= "<pre><code><div class='codebox'>".$outputContent."</div></code></pre>";		
		return $rerurnData;
	}
	
	private function checker($value) {
		global $languageKeywords;		
		$value = trim($value);
		if(isset($languageKeywords[$this->fileExtension])) {				
			if(in_array($value,$languageKeywords[$this->fileExtension]))
			{	$value = "<font class='kC'>".$value."</font>";	}
		}	
		return $value;
	}
}
?>
