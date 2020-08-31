<?php
declare(strict_types=1);

namespace App\Application\Actions\User;

use Psr\Http\Message\ResponseInterface as Response;
use App\Application\Actions\Action;
use App\Libs\Model\CrudModel;

class UpdateUserAction extends Action
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $id = $this->resolveArg('id');
        $params = $this->request->getParsedBody();
        if (empty($params) || !is_array($params)) {
            return $this->invalid('invalidRawContent');
        }

        if (empty($params['account']) || preg_match('/[A-Za-z0-9]+/', $params['account'])) {
            return $this->invalid('accountFormatError');
        } elseif (!filter_var($params['email'], FILTER_VALIDATE_EMAIL)) {
            return $this->invalid('invalidEmail');
        }

        $user = [
            'account' => strtolower($params['account']),
            'name' => $params['name'],
            'gender' => $params['gender'],
            'birthday' => $params['birthday'],
            'email' => $params['email'],
            'note' => $params['note']
        ];

        $model = new CrudModel('account_info');
        $userList = $model->update($user, 'id = :id', [':id' => $id]);
        return $this->success($userList);
    }
}
