<?php
declare(strict_types=1);

namespace App\Application\Actions\User;

use Psr\Http\Message\ResponseInterface as Response;
use App\Application\Actions\Action;
use App\Libs\Model\CrudModel;

class ListUserAction extends Action
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $model = new CrudModel('account_info');
        $userList = $model->select();
        return $this->respondWithData($userList, 200);

        // TODO: render page
    }
}
