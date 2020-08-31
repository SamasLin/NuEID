<?php
declare(strict_types=1);

namespace App\Application\Actions\User;

use Psr\Http\Message\ResponseInterface as Response;
use App\Application\Actions\Action;
use App\Libs\Model\CrudModel;

class DeleteUserAction extends Action
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $id = $this->resolveArg('id');

        $model = new CrudModel('account_info');
        // hard delete
        $model->delete('id = :id', [':id' => $id]);
        // soft delete
        // $model->update(['delete_time' => date('Y-m-d H:i:s')], 'id = :id', [':id' => $id]);
        return $this->success($id);
    }
}
