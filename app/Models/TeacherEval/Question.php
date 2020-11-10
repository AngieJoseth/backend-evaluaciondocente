
class Question extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;

    protected $connection = 'pgsql-teacher-eval';

    protected $fillable = [
        'code',
        'order',
        'name',
        'description',
    ];

    public function evaluationType()
    {
        return $this->belongsTo(EvaluationType::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function type()
    {
        return $this->belongsTo(Catalogue::class, 'type_id');
    }

    public function answers()
    {
        return $this->belongsToMany(Answer::class)->withTimestamps();
    }

    public function status()
    {
        return $this->belongsTo(Catalogue::class, "status_id");
    }

}