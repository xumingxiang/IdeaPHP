<?php

class Razor
{
	private $_input;
	private $_output;
	private $_sourceFile;

	public function generateViewFile($sourceFile, $viewFile){
		$this->_sourceFile = $sourceFile;
		$this->_input = file_get_contents($sourceFile);
        $this->_output = "<?php /* source file: {$sourceFile} */ ?>\n";

        $this->parse(0, strlen($this->_input));
        
		file_put_contents($viewFile, $this->_output);
	}

    private function parse($beginBlock, $endBlock){
        $offset = $beginBlock;
        while (($p = strpos($this->_input, "@", $offset)) !== false && $p < $endBlock) {
            // replace @@ -> @
            if ($this->isNextToken($p, $endBlock, "@")) {
                $this->_output .= substr($this->_input, $offset, $p - $offset + 1);
                $offset = $p + 2;
                continue;
            }

            // replace multi-token statements @(...)
            if ($this->isNextToken($p, $endBlock, "(")) {
                $end = $this->findClosingBracket($p + 1, $endBlock, "(", ")");
                $this->_output .= substr($this->_input, $offset, $p - $offset);
                $this->generatePHPOutput($p, $end);
                $offset = $end + 1;
                continue;
            }

            // replace multi-line statements @{...}
            if ($this->isNextToken($p, $endBlock, "{")) {
                $end = $this->findClosingBracket($p + 1, $endBlock, "{", "}");
                $this->_output .= substr($this->_input, $offset, $p - $offset);
                $this->_output .= "<?php " . substr($this->_input, $p + 2, $end - $p - 2) . " ?>";
                $offset = $end + 1;
                continue;
            }

            // replace HTML-encoded statements @:...
            if ($this->isNextToken($p, $endBlock, ":")) {
                $statement = $this->detectStatement($p + 2, $endBlock);
                $end = $this->findEndStatement($p + 1 + strlen($statement), $endBlock);
                $this->_output .= substr($this->_input, $offset, $p - $offset);
                $this->generatePHPOutput($p + 1, $end, true);
                $offset = $end + 1;
                continue;
            }

            $statement = $this->detectStatement($p + 1, $endBlock);
            if ($statement == "foreach" || $statement == "for" || $statement == "while") {
                $offset = $this->processLoopStatement($p, $offset, $endBlock, $statement);
            } elseif ($statement == "if") {
                $offset = $this->processIfStatement($p, $offset, $endBlock, $statement);
            } else {
                $end = $this->findEndStatement($p + strlen($statement), $endBlock);
                $this->_output .= substr($this->_input, $offset, $p - $offset);
                $this->generatePHPOutput($p, $end);
                $offset = $end + 1;
            }
        }

        $this->_output .= substr($this->_input, $offset, $endBlock - $offset);
    }

    private function generatePHPOutput($currentPosition, $endPosition, $htmlEncode = false){
        $this->_output .= "<?php echo "
                . substr($this->_input, $currentPosition + 1, $endPosition - $currentPosition)
                . "; ?>";
    }

    private function processLoopStatement($currentPosition, $offset, $endBlock, $statement){
        if (($bracketPosition = $this->findOpenBracketAtLine($currentPosition + 1, $endBlock)) === false) {
            throw new RazorViewRendererException("Cannot find open bracket for '{$statement}' statement.",
                    $this->_sourceFile, $this->getLineNumber($currentPosition));
        }

        $this->_output .= substr($this->_input, $offset, $currentPosition - $offset);
        $this->_output .= "<?php " . substr($this->_input, $currentPosition + 1, $bracketPosition - $currentPosition) . " ?>";
        $offset = $bracketPosition + 1;

        $end = $this->findClosingBracket($bracketPosition, $endBlock, "{", "}");
        $this->parse($offset, $end);
        $this->_output .= "<?php } ?>";

        return $end + 1;
    }

    private function processIfStatement($currentPosition, $offset, $endBlock, $statement) {
        $bracketPosition = $this->findOpenBracketAtLine($currentPosition + 1, $endBlock);
        if ($bracketPosition === false) {
            throw new RazorViewRendererException("Cannot find open bracket for '{$statement}' statement.",
                $this->_sourceFile, $this->getLineNumber($currentPosition));
        }

        $this->_output .= substr($this->_input, $offset, $currentPosition - $offset);
        $start = $currentPosition + 1;
        while (true) {
            $this->_output .= "<?php " . substr($this->_input, $start, $bracketPosition - $start + 1) . " ?>";
            $offset = $bracketPosition + 1;

            $end = $this->findClosingBracket($bracketPosition, $endBlock,  "{", "}");
            $this->parse($offset, $end);
            $offset = $end + 1;

            $bracketPosition = $this->findOpenBracketAtLine($offset, $endBlock);
            if ($bracketPosition === false) {
                $this->_output .= "<?php } ?>";
                break;
            }

            $start = $end;
        }

        return $offset;
    }

    private function findOpenBracketAtLine($currentPosition, $endBlock){
        $openDoubleQuotes = false;
        $openSingleQuotes = false;

        for ($p = $currentPosition; $p < $endBlock; ++$p) {
            if ($this->_input[$p] == "\n") {
               // return false;
            }

            $quotesNotOpened = !$openDoubleQuotes && !$openSingleQuotes;
            if ($this->_input[$p] == '"') {
                $openDoubleQuotes = $this->getQuotesState($openDoubleQuotes, $quotesNotOpened, $p);
            } elseif ($this->_input[$p] == "'") {
                $openSingleQuotes = $this->getQuotesState($openSingleQuotes, $quotesNotOpened, $p);
            } elseif ($this->_input[$p] == "{" && $quotesNotOpened) {
                return $p;
            }
        }

        return false;
    }

    private function isNextToken($currentPosition, $endBlock, $token){
        return $currentPosition + strlen($token) < $endBlock
                && substr($this->_input, $currentPosition + 1, strlen($token)) == $token;
    }

    private function isEscaped($currentPosition) {
        $cntBackSlashes = 0;
        for ($p = $currentPosition - 1; $p >= 0; --$p) {
            if ($this->_input[$p] != "\\") {
                break;
            }

            ++$cntBackSlashes;
        }

        return $cntBackSlashes % 2 == 1;
    }

    private function getQuotesState($testedQuotes, $quotesNotOpened, $currentPosition){
        if ($quotesNotOpened) {
            return true;
        }

        return $testedQuotes && !$this->isEscaped($currentPosition) ? false: $testedQuotes;
    }

    private function findClosingBracket($openBracketPosition, $endBlock, $openBracket, $closeBracket) {
        $opened = 0;
        $openDoubleQuotes = false;
        $openSingleQuotes = false;

        for ($p = $openBracketPosition; $p < $endBlock; ++$p) {
            $quotesNotOpened = !$openDoubleQuotes && !$openSingleQuotes;

            if ($this->_input[$p] == '"') {
                $openDoubleQuotes = $this->getQuotesState($openDoubleQuotes, $quotesNotOpened, $p);
            } elseif ($this->_input[$p] == "'") {
                $openSingleQuotes = $this->getQuotesState($openSingleQuotes, $quotesNotOpened, $p);
            } elseif ($this->_input[$p] == $openBracket && $quotesNotOpened) {
                $opened++;
            } elseif ($this->_input[$p] == $closeBracket && $quotesNotOpened) {
                if (--$opened == 0) {
                    return $p;
                }
            }
        }

        throw new RazorViewRendererException("Cannot find closing bracket.", $this->_sourceFile,
                $this->getLineNumber($openBracketPosition));
    }

    private function findEndStatement($endPosition, $endBlock) {
        if ($this->isNextToken($endPosition, $endBlock, "(")) {
            $endPosition = $this->findClosingBracket($endPosition + 1, $endBlock, "(", ")");
            $endPosition = $this->findEndStatement($endPosition, $endBlock);
        } elseif ($this->isNextToken($endPosition, $endBlock, "[")) {
            $endPosition = $this->findClosingBracket($endPosition + 1, $endBlock, "[", "]");
            $endPosition = $this->findEndStatement($endPosition, $endBlock);
        } elseif ($this->isNextToken($endPosition, $endBlock, "->")) {
            $endPosition += 2;
            $statement = $this->detectStatement($endPosition + 1, $endBlock);
            $endPosition = $this->findEndStatement($endPosition + strlen($statement), $endBlock);
        } elseif ($this->isNextToken($endPosition, $endBlock, "::")) {
            $endPosition += 2;
            $statement = $this->detectStatement($endPosition + 1, $endBlock);
            $endPosition = $this->findEndStatement($endPosition + strlen($statement), $endBlock);
        }

        return $endPosition;
    }

    private function detectStatement($currentPosition, $endBlock){
        $invalidCharPosition = $endBlock;
        for ($p = $currentPosition; $p < $invalidCharPosition; ++$p) {
            if ($this->_input[$p] == "$" && $p == $currentPosition) {
                continue;
            }

            if (preg_match('/[a-zA-Z0-9_]/', $this->_input[$p])) {
                continue;
            }

            $invalidCharPosition = $p;
            break;
        }

        if ($currentPosition == $invalidCharPosition) {
            throw new RazorViewRendererException("Cannot detect statement.", $this->_sourceFile,
                $this->getLineNumber($currentPosition));
        }

        return substr($this->_input, $currentPosition, $invalidCharPosition - $currentPosition);
    }

    private function getLineNumber($currentPosition){
		return count(explode("\n", substr($this->_input, 0, $currentPosition)));
	}
}

class RazorViewRendererException 
//extends CException
{
    public function __construct($message, $templateFileName, $line)
    {
	echo $message;
	echo "\r\n";
	echo   $templateFileName;
	echo "\r\n";
	echo  $line;
       // parent::__construct("Invalid view template: {$templateFileName}, at line {$line}. {$message}", null, null);
    }
}