<?php declare(strict_types = 1);

namespace App;

class ReverseTemplate
{
    private string $startVariable = '{';
    private string $endVariable = '}';

    public function execute(string $template, string $resultString): array
    {
        if (!$this->templateValidator($template)) {
            throw new InvalidTemplateException();
        }

        if (!preg_match_all($this->getPregTemplate($template), $resultString, $matches)) {
            throw new ResultTemplateMismatchException();
        }

        $variables = $this->getVariablesInfo($template);

        $result = [];

        $variablesNames = array_keys($variables);
        $numberVariables = count($variables);

        for ($i = 1; $i <= $numberVariables; $i++) {
            $key = $i - 1;
            $value = $matches[$i][0];
            $variableName = $variablesNames[$key];
            $htmlEscaped = $variables[$variableName];
            $result [$variableName] = $htmlEscaped ? htmlspecialchars_decode($value) : $value;
        }

        return $result;
    }

    private function templateValidator(string $template): bool
    {
        $template = str_split($template);
        $buffer = 0;
        $variableExists = false;

        foreach ($template as $item) {
            if ($item === $this->startVariable) {
                $buffer++;
            }

            if ($item === $this->endVariable) {
                if ($buffer === 0) {
                    return false;
                }
                $buffer--;
                $variableExists = true;
            }
        }

        return $buffer === 0 && $variableExists;
    }

    private function getVariablesInfo(string $template): array
    {
        $variables = [];

        preg_match_all($this->getCommonTemplateVariable(), $template, $matches);

        foreach ($matches[0] as $key => $match) {

            $variable = $matches[1][$key];
            $htmlEscaped = preg_match($this->getTemplateEscapedVariable(), $match);

            $variables [$variable] = $htmlEscaped;
        }

        return $variables;
    }

    private function getPregTemplate(string $template): string
    {
        return '/' . preg_replace($this->getCommonTemplateVariable(), '(.*)', $template) . '/';
    }

    private function getTemplateEscapedVariable(): string
    {
        return "/{$this->startVariable}{$this->startVariable}([^{$this->startVariable}{$this->endVariable}]*){$this->endVariable}{$this->endVariable}/";
    }

    private function getCommonTemplateVariable(): string
    {
        return "/{$this->startVariable}{$this->startVariable}?([^{$this->startVariable}{$this->endVariable}]*){$this->endVariable}{$this->endVariable}?/";
    }
}
