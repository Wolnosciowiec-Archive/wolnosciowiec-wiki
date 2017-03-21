<?php declare(strict_types=1);

namespace WikiBundle\Domain\Context;

class FileProcessContext
{
    protected $path = '';
    protected $repositoryName = '';
    protected $repositoryPath = '';
    protected $triggerEvents = true;
    protected $compilationOptions = [];
    protected $variables = [];
    protected $compiledContent = '';
    protected $forceCompile = false;

    public function __construct(array $parameters)
    {
        foreach ($parameters as $name => $value) {
            $methodName = 'set' . ucfirst($name);

            if (!method_exists($this, $methodName)) {
                throw new \InvalidArgumentException('Invalid parameter "' . $name . '" passed to the $parameters');
            }

            $this->$methodName($value);
        }
    }

    public function setPath($path): FileProcessContext
    {
        $this->path = $path;
        return $this;
    }

    public function setRepositoryName($repositoryName): FileProcessContext
    {
        $this->repositoryName = $repositoryName;
        return $this;
    }

    public function setRepositoryPath($repositoryPath): FileProcessContext
    {
        $this->repositoryPath = $repositoryPath;
        return $this;
    }

    public function setTriggerEvents($triggerEvents): FileProcessContext
    {
        $this->triggerEvents = $triggerEvents;
        return $this;
    }

    public function setCompilationOptions($compilationOptions): FileProcessContext
    {
        $this->compilationOptions = $compilationOptions;
        return $this;
    }

    public function setVariables($variables): FileProcessContext
    {
        $this->variables = $variables;
        return $this;
    }

    public function setCompiledContent(string $compiledContent): FileProcessContext
    {
        $this->compiledContent = $compiledContent;
        return $this;
    }

    public function setForceCompile(bool $forceCompile): FileProcessContext
    {
        $this->forceCompile = $forceCompile;
        return $this;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getRepositoryName(): string
    {
        return $this->repositoryName;
    }

    public function getRepositoryPath(): string
    {
        return $this->repositoryPath;
    }

    public function willTriggerEvents(): bool
    {
        return $this->triggerEvents;
    }

    public function getCompilationOptions(): array
    {
        return $this->compilationOptions;
    }

    public function getVariables(): array
    {
        return $this->variables;
    }

    public function getCompiledContent(): string
    {
        return $this->compiledContent;
    }

    public function isCompilationForced(): bool
    {
        return $this->forceCompile;
    }
}
