<?php namespace Riari\Forum\Models;

use Cache;
use Illuminate\Database\Eloquent\Model;

abstract class BaseModel extends Model
{
    /*
    |--------------------------------------------------------------------------
    | Attributes
    |--------------------------------------------------------------------------
    */

    public function getPostedAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    public function getUpdatedAttribute()
    {
        return $this->updated_at->diffForHumans();
    }

    protected function rememberAttribute($item, $function)
    {
        $cacheItem = get_class($this).$this->id.$item;

        $value = Cache::remember($cacheItem, config('forum.preferences.cache.lifetime'), $function);

        return $value;
    }

    protected static function clearAttributeCache($model)
    {
        foreach ($model->appends as $attribute) {
            $cacheItem = get_class($model).$model->id.$attribute;
            Cache::forget($cacheItem);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Determine if this model has been updated since the given model.
     *
     * @param  Model  $model
     * @return boolean
     */
    public function updatedSince(&$model)
    {
        return ($this->updated_at > $model->updated_at);
    }

    /**
     * Determine if the current user has the given permission for this model.
     *
     * @param  string  $permission
     * @return boolean
     */
    protected function userCan($permission)
    {
        return permitted($this->getAccessParams(), $permission, auth()->user());
    }

    /**
     * Return an array of components used to construct this model's route.
     *
     * @return array
     */
    protected function getRouteComponents()
    {
        $components = [];
        return $components;
    }

    /**
     * Return an array of parameters used by the userCan() method to check
     * permissions.
     *
     * @return array
     */
    protected function getAccessParams()
    {
        $parameters = [];
        return $parameters;
    }

    /**
     * Return a route of the given name using the current and specified route
     * components.
     *
     * @param  string  $name
     * @param  array  $components
     * @return string
     */
    protected function getRoute($name, $components = array())
    {
        return route($name, array_merge($this->getRouteComponents(), $components));
    }

    /**
     * Toggle an attribute on this model.
     *
     * @param  string  $attribute
     * @return void
     */
    public function toggle($attribute)
    {
        $this->$attribute = !$this->$attribute;
        $this->save();
    }
}
