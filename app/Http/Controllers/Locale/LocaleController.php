<?php 
namespace App\Http\Controllers\Locale;

use App\Http\Controllers\Controller;
use Careminate\ServiceProviders\LocaleService;

class LocaleController extends Controller
{
    protected LocaleService $localeService;

    public function __construct(LocaleService $localeService)
    {
        $this->localeService = $localeService;
    }

    public function setLocale($locale)
    {
        // Set locale using the LocaleService
        $this->localeService->setLocale($locale);

        // Return the response
        return response('Locale has been set to ' . $locale);
    }

    public function showLocale()
    {
        // Get the current locale
        $locale = $this->localeService->getLocale();
        
        // Return the response
        return view('home',['locale'=>$locale]);
    }
}
