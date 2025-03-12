<?php

namespace app\models;

use yii\db\ActiveRecord;
use yii\db\Expression;

class BaseModel extends ActiveRecord
{

    public function fields()
    {
        $fields = parent::fields();

        if (isset($fields['updated_at'])) {
            $fields['updated_at'] = function () {
                return $this->updated_at instanceof Expression
                    ? date('Y-m-d H:i:s')
                    : $this->updated_at;
            };

            $fields['created_at'] = function () {
                return $this->created_at instanceof Expression
                    ? date('Y-m-d H:i:s')
                    : $this->created_at;
            };
        }

        return $fields;
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if ($insert) {
            $this->created_at = $this->getAttribute('created_at');
            $this->updated_at = $this->getAttribute('updated_at');
        }
    }
}
