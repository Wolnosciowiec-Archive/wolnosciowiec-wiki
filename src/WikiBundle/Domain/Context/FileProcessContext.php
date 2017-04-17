<?php declare(strict_types=1);

namespace WikiBundle\Domain\Context;

class FileProcessContext extends AbstractContext
{
    protected $path = '';
    protected $repositoryName = '';
    protected $repositoryPath = '';
    protected $triggerEvents = true;
    protected $compilationOptions = [];
    protected $variables = [];
    protected $compiledContent = '';
    protected $forceCompile = false;
    protected $targetPath = '';
    protected $page = 1;

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

    public function setVariables(array $variables): FileProcessContext
    {
        $this->variables = $variables;
        return $this;
    }

    public function addVariables(array $variables): FileProcessContext
    {
        $this->variables = array_merge($this->variables, $variables);
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

    public function setTargetPath(string $targetPath): FileProcessContext
    {
        $this->targetPath = $targetPath;
        return $this;
    }

    public function getTargetPath(): string
    {
        return $this->targetPath;
    }

    public function setPage($page): FileProcessContext
    {
        $this->page = $page;
        return $this;
    }

    public function getPage(): int
    {
        return $this->page;
    }
}
