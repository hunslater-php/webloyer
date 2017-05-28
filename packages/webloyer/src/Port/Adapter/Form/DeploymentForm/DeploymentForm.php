<?php

namespace Ngmy\Webloyer\Webloyer\Port\Adapter\Form\DeploymentForm;

use Ngmy\Webloyer\Webloyer\Application\Deployment\DeploymentService;
use Ngmy\Webloyer\Webloyer\Application\Project\ProjectService;
use Ngmy\Webloyer\Webloyer\Application\Deployer\DeployerService;
use Ngmy\Webloyer\Common\Validation\ValidableInterface;

class DeploymentForm
{
    private $validator;

    private $deploymentService;

    private $projectService;

    private $deployerService;

    /**
     * Create a new form service instance.
     *
     * @param \Ngmy\Webloyer\Common\Validation\ValidableInterface              $validator
     * @param \Ngmy\Webloyer\Webloyer\Application\Deployment\DeploymentService $deploymentService
     * @param \Ngmy\Webloyer\Webloyer\Application\Project\ProjectService       $projectService
     * @param \Ngmy\Webloyer\Webloyer\Application\Deployer\DeployerService     $deployerService
     * @return void
     */
    public function __construct(ValidableInterface $validator, DeploymentService $deploymentService, ProjectService $projectService, DeployerService $deployerService)
    {
        $this->validator = $validator;
        $this->deploymentService = $deploymentService;
        $this->projectService = $projectService;
        $this->deployerService = $deployerService;
    }

    /**
     * Create a new deployment.
     *
     * @param array $input Data to create a deployment
     * @return boolean
     */
    public function save(array $input)
    {
        if (!$this->valid($input)) {
            return false;
        }

        $deployment = $this->deploymentService->saveDeployment(
            $input['project_id'],
            $this->deploymentService->getNextDeploymentIdOfProject($input['project_id'])->id(),
            $input['task'],
            null,
            null,
            $input['user_id']
        );

        $this->deployerService->dispatchDeployer(
            $deployment->projectId()->id(),
            $deployment->deploymentId()->id()
        );

        return true;
    }

    /**
     * Return validation errors.
     *
     * @return array
     */
    public function errors()
    {
        return $this->validator->errors();
    }

    /**
     * Test whether form validator passes.
     *
     * @return boolean
     */
    protected function valid(array $input)
    {
        return $this->validator->with($input)->passes();
    }
}
