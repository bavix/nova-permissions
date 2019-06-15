<?php
namespace Bavix\NovaPermissions\Nova;

use Laravel\Nova\Nova;
use Laravel\Nova\Resource;
use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Text;
use Illuminate\Validation\Rule;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\MorphToMany;
use Laravel\Nova\Fields\BelongsToMany;
use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends Resource
{

    /**
     * @var mixed
     */
    public static $displayInNavigation = false;

    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = SpatiePermission::class;

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'name',
    ];

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function fields(Request $request)
    {
        $guardOptions = collect(config('auth.guards'))->mapWithKeys(function ($value, $key) {
            return [$key => $key];
        });

        $userResource = Nova::resourceForModel(getModelForGuard($this->guard_name));

        return [
            ID::make('Id', 'id')
                ->rules('required')
                ->hideFromIndex(),

            Text::make(__('Name'), 'name')
                ->rules(['required', 'string', 'max:255'])
                ->creationRules('unique:' . config('permission.table_names.permissions'))
                ->updateRules('unique:' . config('permission.table_names.permissions') . ',name,{{resourceId}}'),

            Select::make(__('Guard Name'), 'guard_name')
                ->options($guardOptions->toArray())
                ->rules(['required', Rule::in($guardOptions)]),

            BelongsToMany::make(__('Roles'), 'roles', Role::class),
            MorphToMany::make($userResource::label(), 'users', $userResource)->searchable(),
        ];
    }

    /**
     * @return string
     */
    public static function label(): string
    {
        return __('Permissions');
    }

    /**
     * @return string
     */
    public static function singularLabel(): string
    {
        return __('Permission');
    }

}
