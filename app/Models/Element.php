<?php

namespace App\Models;

use App\Helpers\Macros\Traits\Languages;
use Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Element
 *
 * @property int                                                                          $id
 * @property \Carbon\Carbon                                                               $created_at
 * @property \Carbon\Carbon                                                               $updated_at
 * @property string                                                                       $deleted_at
 * @property int                                                                          $created_user_id
 * @property int                                                                          $updated_user_id
 * @property int                                                                          $deleted_user_id
 * @property int                                                                          $schema_id
 * @property string                                                                       $name
 * @property string                                                                       $label
 * @property string                                                                       $definition
 * @property string                                                                       $comment
 * @property string                                                                       $type
 * @property int                                                                          $is_subproperty_of
 * @property string                                                                       $parent_uri
 * @property string                                                                       $uri
 * @property int                                                                          $status_id
 * @property string                                                                       $language
 * @property string                                                                       $note
 * @property string                                                                       $domain
 * @property string                                                                       $orange
 * @property bool                                                                         $is_deprecated Boolean. Has this class/property been deprecated
 * @property string                                                                       $url
 * @property string                                                                       $lexical_alias
 * @property string                                                                       $hash_id
 * @property int                                                                          $created_by
 * @property int                                                                          $updated_by
 * @property int                                                                          $deleted_by
 * @property-read \App\Models\Elementset                                                  $elementset
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ElementAttribute[] $properties
 * @property-read \App\Models\Status                                                      $status
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Element whereComment( $value )
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Element whereCreatedAt( $value )
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Element whereCreatedBy( $value )
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Element whereCreatedUserId( $value )
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Element whereDefinition( $value )
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Element whereDeletedAt( $value )
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Element whereDeletedBy( $value )
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Element whereDeletedUserId( $value )
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Element whereDomain( $value )
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Element whereHashId( $value )
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Element whereId( $value )
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Element whereIsDeprecated( $value )
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Element whereIsSubpropertyOf( $value )
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Element whereLabel( $value )
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Element whereLanguage( $value )
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Element whereLexicalAlias( $value )
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Element whereName( $value )
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Element whereNote( $value )
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Element whereOrange( $value )
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Element whereParentUri( $value )
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Element whereSchemaId( $value )
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Element whereStatusId( $value )
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Element whereType( $value )
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Element whereUpdatedAt( $value )
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Element whereUpdatedBy( $value )
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Element whereUpdatedUserId( $value )
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Element whereUri( $value )
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Element whereUrl( $value )
 * @mixin \Eloquent
 */
class Element extends Model
{
    protected $table = self::TABLE;

    const TABLE = 'reg_schema_property';

    protected $primaryKey = 'id';

    use SoftDeletes, Languages;

    public function elementset()
    {
        return $this->belongsTo( \App\Models\Elementset::class, 'schema_id', 'id' );
    }

    public function properties()
    {
        return $this->hasMany( \App\Models\ElementAttribute::class, 'schema_property_id', 'id' );
    }

    public function status()
    {
        return $this->belongsTo( \App\Models\Status::class, 'status_id', 'id' );
    }

    public function getLanguageAttribute( $value )
    {
        return Cache::get( 'language_' . $value,
            function() use ( $value ) {
                return Languages::list( $value );
            } );
    }

    /**
     * @param int $projectId
     *
     * @return array
     */
    public static function SelectElementsByProject( $projectId )
    {
        return \DB::table( ElementAttribute::TABLE )
            ->join( Element::TABLE,
                Element::TABLE . '.id',
                '=',
                ElementAttribute::TABLE . '.schema_property_id' )
            ->join( Elementset::TABLE,
                Elementset::TABLE . '.id',
                '=',
                Element::TABLE . '.schema_id' )
            ->select( ElementAttribute::TABLE .
                '.schema_property_id as id',
                Elementset::TABLE . '.name as Elementset',
                ElementAttribute::TABLE . '.language',
                ElementAttribute::TABLE . '.object as label' )
            ->where( [
                [ ElementAttribute::TABLE . '.profile_property_id', 2, ],
                [ Elementset::TABLE . '.agent_id', $projectId, ],
            ] )
            ->orderBy( Elementset::TABLE . '.name' )
            ->orderBy( ElementAttribute::TABLE .
                '.language' )
            ->orderBy( ElementAttribute::TABLE . '.object' )
            ->get()
            ->mapWithKeys( function( $item ) {
                return [
                    $item->id . '_' . $item->language => $item->Elementset .
                        ' - (' .
                        $item->language .
                        ') ' .
                        $item->label,
                ];
            } )
            ->toArray();
    }

}
