<?php

namespace App\Support;

/**
 * Gestion centralisée du logo de l'application.
 *
 * Déposez le fichier image dans public/images/ sous le nom
 * logo.png, logo.jpg ou logo.jpeg : il sera détecté automatiquement
 * et affiché dans l'application ainsi que sur les exports PDF et Excel.
 */
class AppLogo
{
    private const CANDIDATES = ['logo.png', 'logo.jpg', 'logo.jpeg'];

    /**
     * Chemin relatif (depuis public/) du logo, ou null s'il n'existe pas.
     */
    public static function relativePath(): ?string
    {
        foreach (self::CANDIDATES as $file) {
            if (file_exists(public_path('images/' . $file))) {
                return 'images/' . $file;
            }
        }

        return null;
    }

    public static function exists(): bool
    {
        return self::relativePath() !== null;
    }

    /**
     * URL publique du logo (pour les pages web).
     */
    public static function url(): ?string
    {
        $path = self::relativePath();

        return $path ? asset($path) : null;
    }

    /**
     * Chemin absolu du fichier (pour les exports Excel).
     */
    public static function publicPath(): ?string
    {
        $path = self::relativePath();

        return $path ? public_path($path) : null;
    }

    /**
     * Contenu du logo encodé en base64 (pour les PDF DomPDF).
     */
    public static function base64(): ?string
    {
        $absolute = self::publicPath();

        if (!$absolute) {
            return null;
        }

        $mime = mime_content_type($absolute) ?: 'image/png';

        return 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($absolute));
    }
}
