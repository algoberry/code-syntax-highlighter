<?php
include_once("keywords.php");
class Highlighter {
	private $fileName;
	private $fileExtension;
	private $showFileName;
	
	public function __construct() {
		$this->fileName = "";
		$this->fileExtension = "";
		$this->showFileName = true;
	}
	
	public function showfilename($value) {
		$this->showFileName = $value;
	}

	//Hello World Baba

	public function applycolor($fileLocation = "") {
		$temp1 = "Wikis are enabled by wiki software, otherwise known as wiki engines. A wiki engine, being a form of a content management system, 
		differs from other web-based systems such as blog software, in that the content is created without any defined owner or leader, and wikis have little inherent structure, allowing structure to emerge according to the needs of the users.[1] Wiki engines usually allow content to be written using a simplified markup language and sometimes edited with the help of a rich-text editor.[2] There are dozens of different wiki engines in use, both standalone and part of other software, such as bug tracking systems. Some wiki engines are open-source, whereas others are proprietary. Some permit control over different functions (levels of access); for example, editing rights may permit changing, adding, or removing material. Others may permit access without enforcing access control. Other rules may be imposed to organize content.";
		if($fileLocation == "") {
			return;
		}
		else
		{
			if(file_exists($fileLocation)) {
				$temp = explode("/",$fileLocation);
				$this->fileName = trim(end($temp));
				$temp = explode(".",$this->fileName);
				$this->fileExtension = trim(end($temp));
				$fileContent = trim(file_get_contents($fileLocation, true));
				$fileContent = htmlentities($fileContent,ENT_NOQUOTES);
				if($fileContent == "") {	
					return;	
				}
			}
			else
			{	
				return; 
			}
			
			$parenthesisFound = 0;
			$bracketFound = 0;
			$foundCharacter = "";

			$line = 1;
			$counter = 0;
			$contentSize = strlen($fileContent);

			$content = "<font class='lI'>".$line."</font> ";
			while($counter < $contentSize) {
				$character = $fileContent[$counter];
				$code = intval(ord($character));
				if(($code >= 97 && $code <= 122) || ($code >= 65 && $code <= 90)) {	
					$characterBuffer .= $character;	
				}
				else
				{
					if($characterBuffer != "") {	
						$content .= $this->checker($characterBuffer);
						$characterBuffer = "";
					}

					if($character == "/" && (isset($fileContent[$counter+1]) && ($fileContent[$counter+1] == "*" || $fileContent[$counter+1] == "/"))) {
						$content .= "<font class='cC'>".$fileContent[$counter].$fileContent[$counter+1];
						if($fileContent[$counter+1] == "*") {
							$counter += 2;
							while($counter < $contentSize) {
								$character = $fileContent[$counter];
								$code = intval(ord($character));
								if($code != 10) {
									if($character == "*" && (isset($fileContent[$counter+1]) && ($fileContent[$counter+1] == "/"))) {
										$counter++;
										$content .= $character.$fileContent[$counter]."</font>";
										break;
									}
									else
									{	$content .= $character;	}
								}
								else
								{
									$line++;
									$content .= "</font>".$character."<font class='lI'>".$line."</font> <font class='cC'>";
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
									$content .= "</font>";
									$counter--;
									break;
								}
								$content .= $character;
								$counter++;
							}
						}
					}
					else if($character == "'" || $character == "\"") {
						$foundCharacter = $character;
						$content .= "<font class='qC'>".$foundCharacter;
						$counter++;
						while($counter < $contentSize) {
							$character = $fileContent[$counter];
							$code = intval(ord($character));
							if($foundCharacter == $character) {
								if($foundCharacter == "\"") {
									if($fileContent[$counter-1] != "\\") {
										$content .= $foundCharacter."</font>";
										break;
									}
									else if($fileContent[$counter-2] == "\\" && $fileContent[$counter-1] == "\\") {
										$content .= $foundCharacter."</font>";
										break;
									}
									else
									{
										$content .= $character;
									}
								}
								else
								{
									$content .= $foundCharacter."</font>";
									break;
								}
							}
							else if($code == 10) {
								$line++;
								$content .= $character;
								$content .= "<font class='lI'>".$line."</font> ";
							}
							else
							{
								$content .= $character;
							}
							$counter++;
						}
					}
					else if($character == "(" || $character == ")") {
						if($parenthesisFound == 0) {
							$content .= "<font class='pC'>".$character."</font><font class='iPC'>";
						}
						if($character == "(") {
							$parenthesisFound++;
						}
						else if($character == ")") {
							$parenthesisFound--;
						}
						if($parenthesisFound == 0) {
							$content .= "</font><font class='pC'>".$character."</font>";
						}
					}
					else if($character == "[" || $character == "]") {
						if($bracketFound == 0) {
							$content .= "<font class='bC'>".$character."</font><font class='iBC'>";
						}
						if($character == "[") {
							$bracketFound++;
						}
						else if($character == "]") {
							$bracketFound--;
						}
						if($bracketFound == 0) {
							$content .= "</font><font class='bC'>".$character."</font>";
						}
					}
					else if($code == 10) {
						$line++;
						$content .= $character;
						$content .= "<font class='lI'>".$line."</font> ";
					}
					else
					{
						$content .= $character;
					}
				}
				$counter++;
			}

			$output .= "<div class='codebox'>";
			if($this->showFileName == true) {
				$output .= "<div class='fN'>".$this->fileName."</div>";
			}
			$output .= "<div class='code'><pre><code>".$content."</code></pre></div>";
			$output .= "</div>";
			return $output;
		}
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