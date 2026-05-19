<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'name', 
    'email', 
    'password', 
    'perfil', 
    'role', 
    'area', 
    'area_color', 
    'area_bg', 
    'initials'
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // ========== RELACIONES ==========
    
    /**
     * Proyectos donde este usuario es el ingeniero asignado
     */
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class, 'engineer_id');
    }

    /**
     * Notas creadas por este usuario
     */
    public function notes(): HasMany
    {
        return $this->hasMany(ProjectNote::class);
    }

    /**
     * Aprobaciones realizadas por este usuario
     */
    public function approvals(): HasMany
    {
        return $this->hasMany(GateApproval::class, 'approved_by');
    }

    // ========== MÉTODOS DE UTILIDAD ==========

    /**
     * Verifica si el usuario es Gerente de Innovación (GI)
     */
    public function isGerente(): bool
    {
        return $this->perfil === 'GI';
    }

    /**
     * Verifica si el usuario es un área de soporte (Calidad, Costos, Procesos)
     */
    public function isArea(): bool
    {
        return in_array($this->perfil, ['CAL', 'COS', 'PRO']);
    }

    /**
     * Verifica si el usuario es Ingeniero de Producto
     */
    public function isIngeniero(): bool
    {
        return in_array($this->perfil, ['RB', 'FR', 'AE', 'PR', 'RE']);
    }

    /**
     * Verifica si el usuario puede editar un proyecto específico
     * - Gerente: puede editar todos
     * - Ingeniero: solo sus proyectos
     * - Área: solo tareas específicas (se valida aparte)
     */
    public function canEditProject(Project $project): bool
    {
        if ($this->isGerente()) {
            return true;
        }
        
        if ($this->isIngeniero()) {
            return $project->engineer_id === $this->id;
        }
        
        // Las áreas tienen permisos específicos por tarea, no por proyecto completo
        return false;
    }

    /**
     * Obtiene las tareas que este usuario de área puede editar
     */
    public function getAreaTasks(): array
    {
        $areaTasks = [
            'CAL' => [
                'Validación dimensional',
                'Pruebas estéticas y de acabados',
                'Pruebas de uso, resistencia, carga y estabilidad',
                'Liberación dimensional de plantillas',
                'Liberación de piezas de corrida piloto',
                'Validación de plantillas post-piloto',
                'Revisar calidad en primeras entregas',
                'Seguimiento a reclamos, fallas o incidencias',
            ],
            'COS' => [
                'Validación de costo objetivo inicial',
                'Costo preliminar de ingeniería',
                'Costo industrial validado',
                'Alta completa en ERP',
                'Asignación de SKU',
                'Ajustes en ERP y costo final liberado',
                'Definición de precios de lista',
                'Alta en aplicativo y cotizador',
                'Validar costo real vs. objetivo',
            ],
            'PRO' => [
                'Revisión preliminar de factibilidad de manufactura',
                'Evaluación de diseño para manufactura y ensamble',
                'Fabricación de prototipos funcionales',
                'Fabricación de plantillas',
                'Flujo de proceso y ruta de fabricación',
                'Instrucciones de trabajo y ayudas visuales',
                'Validación de tiempos preliminares',
                'Corrida piloto controlada mínimo 3 pzas.',
                'Liberación de producción y procesos',
                'Validar tiempo real vs. objetivo',
            ],
        ];

        return $areaTasks[$this->perfil] ?? [];
    }

    /**
     * Obtiene el color de iniciales para el avatar
     */
    public function getAvatarStyle(): array
    {
        $colors = [
            'GI' => ['bg' => '#1a1f3c', 'text' => '#ffffff'],
            'RB' => ['bg' => '#EEEDFE', 'text' => '#3C3489'],
            'FR' => ['bg' => '#E6F1FB', 'text' => '#0C447C'],
            'AE' => ['bg' => '#E1F5EE', 'text' => '#085041'],
            'PR' => ['bg' => '#FAEEDA', 'text' => '#633806'],
            'RE' => ['bg' => '#FAECE7', 'text' => '#712B13'],
            'CAL' => ['bg' => '#E6F1FB', 'text' => '#185FA5'],
            'COS' => ['bg' => '#E1F5EE', 'text' => '#0F6E56'],
            'PRO' => ['bg' => '#FAEEDA', 'text' => '#854F0B'],
        ];

        return $colors[$this->perfil] ?? ['bg' => '#e5e7eb', 'text' => '#374151'];
    }

    /**
     * Obtiene las iniciales del usuario
     */
    public function getInitials(): string
    {
        if ($this->initials) {
            return $this->initials;
        }

        $words = explode(' ', $this->name);
        if (count($words) >= 2) {
            return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        }
        
        return strtoupper(substr($this->name, 0, 2));
    }
}