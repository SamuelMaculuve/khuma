<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class PlanFeatureServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        /**
         * @planfeature('members')
         *   <p>Só aparece se o plano tiver a feature 'members'</p>
         * @endplanfeature
         */
        Blade::directive('planfeature', function (string $key) {
            return "<?php if(auth()->check() && auth()->user()->hasFeature({$key})): ?>";
        });

        Blade::directive('endplanfeature', function () {
            return '<?php endif; ?>';
        });

        /**
         * @planunlimited('chatbot_lines')
         *   <span>Ilimitado</span>
         * @else
         *   <span>{{ auth()->user()->featureValue('chatbot_lines') }} linhas</span>
         * @endplanunlimited
         */
        Blade::directive('planunlimited', function (string $key) {
            return "<?php if(auth()->check() && auth()->user()->featureIsUnlimited({$key})): ?>";
        });

        Blade::directive('endplanunlimited', function () {
            return '<?php endif; ?>';
        });

        /**
         * @planlimit('members', $team->members()->count())
         *   <button>Adicionar membro</button>
         * @endplanlimit
         */
        Blade::directive('planlimit', function (string $expression) {
            [$key, $usage] = array_map('trim', explode(',', $expression, 2));
            return "<?php if(auth()->check() && auth()->user()->canUseFeature({$key}, {$usage})): ?>";
        });

        Blade::directive('endplanlimit', function () {
            return '<?php endif; ?>';
        });
    }
}
