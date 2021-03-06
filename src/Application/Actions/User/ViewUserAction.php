<?php
declare(strict_types=1);

namespace App\Application\Actions\User;

use Psr\Http\Message\ResponseInterface as Response;
use App\Application\Actions\Action;
use App\Libs\Model\CrudModel;

class ViewUserAction extends Action
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $id = $this->resolveArg('id');

        $model = new CrudModel('account_info');
        $user = $model->select('id = :id AND delete_time IS NULL', [':id' => $id]);
        return $this->success($user);
    }
}
