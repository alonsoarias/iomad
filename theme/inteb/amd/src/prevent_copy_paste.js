// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Copy/paste and screenshot prevention JavaScript module.
 *
 * @module     theme_inteb/prevent_copy_paste
 * @copyright  2025 Vicerrectoría Académica ISER <vicerrectoria@iser.edu.co>
 * @author     Alonso Arias <soporteplataformas@iser.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery'], function($) {
    'use strict';

    var SELECTORS = {
        ALLOWED_ELEMENTS: 'input, textarea, [contenteditable="true"], .editor_atto_content, .atto_text, [role="textbox"]'
    };

    var PreventCopyPaste = function() {
        this.registerEventListeners();
        this.disableSelection();
        this.preventScreenshot();
    };

    PreventCopyPaste.prototype.registerEventListeners = function() {
        var self = this;

        // Función mejorada para verificar elementos permitidos
        function isElementAllowed(element) {
            if (!element) return false;

            var $element = $(element);

            // Verificar si el elemento en sí está permitido
            if ($element.is(SELECTORS.ALLOWED_ELEMENTS)) {
                return true;
            }

            // Verificar si está dentro de un contenedor permitido
            if ($element.closest(SELECTORS.ALLOWED_ELEMENTS).length > 0) {
                return true;
            }

            // Verificar atributos específicos de Moodle
            if ($element.closest('[contenteditable="true"]').length > 0) {
                return true;
            }

            // Verificar clases específicas de editores de Moodle
            if ($element.closest('.editor_atto_content, .atto_text').length > 0) {
                return true;
            }

            return false;
        }

        // Prevent right click context menu
        $(document).on('contextmenu', function(e) {
            if (!isElementAllowed(e.target)) {
                e.preventDefault();
                return false;
            }
        });

        // Prevent keyboard shortcuts
        $(document).on('keydown', function(e) {
            if (!isElementAllowed(e.target)) {
                if (e.ctrlKey || e.metaKey) {
                    // Usar e.key si está disponible, sino usar e.keyCode
                    var key = e.key ? e.key.toLowerCase() : '';
                    var keyCode = e.keyCode || e.which;

                    // Lista extendida de atajos a prevenir
                    var blockedKeys = ['c', 'v', 'x', 'a', 's', 'u', 'p', 'j', 'd', 'f', 'g', 'h', 'i', 'k', 'w'];
                    var blockedKeyCodes = [67, 86, 88, 65, 83, 85, 80, 74, 68, 70, 71, 72, 73, 75, 87];

                    if (blockedKeys.indexOf(key) !== -1 || blockedKeyCodes.indexOf(keyCode) !== -1) {
                        e.preventDefault();
                        e.stopPropagation();
                        return false;
                    }
                }

                // Prevent F12 (Dev Tools) - usar tanto e.key como e.keyCode para compatibilidad
                if (e.key === 'F12' || e.keyCode === 123 || e.which === 123) {
                    e.preventDefault();
                    return false;
                }

                // Prevent Ctrl+Shift+I/J/C (Dev Tools)
                if (e.ctrlKey && e.shiftKey) {
                    var shiftKeyCode = e.keyCode || e.which;
                    if (shiftKeyCode === 73 || shiftKeyCode === 74 || shiftKeyCode === 67) { // I, J, C
                        e.preventDefault();
                        e.stopPropagation();
                        return false;
                    }
                }

                // Prevent Alt+PrintScreen
                if (e.altKey && (e.key === 'PrintScreen' || e.keyCode === 44)) {
                    e.preventDefault();
                    return false;
                }

                // Prevent PrintScreen
                if (e.key === 'PrintScreen' || e.keyCode === 44) {
                    e.preventDefault();
                    return false;
                }
            }
        });

        // Prevent copy/paste/cut
        $(document).on('copy paste cut', function(e) {
            if (!isElementAllowed(e.target)) {
                e.preventDefault();
                return false;
            }
        });

        // Prevent drag and drop
        $(document).on('dragstart drop', function(e) {
            if (!isElementAllowed(e.target)) {
                e.preventDefault();
                return false;
            }
        });

        // Prevent text selection
        $(document).on('selectstart', function(e) {
            if (!isElementAllowed(e.target)) {
                e.preventDefault();
                return false;
            }
        });

        // Prevent print dialog
        if (window.addEventListener) {
            window.addEventListener('beforeprint', function(e) {
                e.preventDefault();
                return false;
            });
        } else if (window.attachEvent) {
            // IE compatibility
            window.attachEvent('onbeforeprint', function(e) {
                e.returnValue = false;
                return false;
            });
        }
    };

    PreventCopyPaste.prototype.disableSelection = function() {
        // Aplicar CSS directamente al body
        $('body').css({
            '-webkit-user-select': 'none',
            '-moz-user-select': 'none',
            '-ms-user-select': 'none',
            'user-select': 'none',
            '-webkit-touch-callout': 'none',
            '-webkit-tap-highlight-color': 'transparent'
        });

        // También agregar los estilos al head para mayor robustez
        if ($('#prevent-copy-paste-styles').length === 0) {
            var css = '<style id="prevent-copy-paste-styles">' +
                'body { ' +
                '-webkit-user-select: none !important; ' +
                '-moz-user-select: none !important; ' +
                '-ms-user-select: none !important; ' +
                'user-select: none !important; ' +
                '-webkit-touch-callout: none !important; ' +
                '-webkit-tap-highlight-color: transparent !important; ' +
                '}' +
                'input, textarea, [contenteditable="true"], .editor_atto_content, .atto_text, [role="textbox"] { ' +
                '-webkit-user-select: text !important; ' +
                '-moz-user-select: text !important; ' +
                '-ms-user-select: text !important; ' +
                'user-select: text !important; ' +
                '}' +
                '/* Permitir selección dentro de editores de Moodle */' +
                '.editor_atto_content *, .atto_text *, [contenteditable="true"] * { ' +
                '-webkit-user-select: text !important; ' +
                '-moz-user-select: text !important; ' +
                '-ms-user-select: text !important; ' +
                'user-select: text !important; ' +
                '}' +
                '::selection { background: transparent; }' +
                '::-moz-selection { background: transparent; }' +
                '/* Pero permitir selección en editores */' +
                'input::selection, textarea::selection, [contenteditable="true"]::selection, ' +
                '.editor_atto_content ::selection, .atto_text ::selection { ' +
                'background: #b3d4fc !important; ' +
                '}' +
                'input::-moz-selection, textarea::-moz-selection, [contenteditable="true"]::-moz-selection, ' +
                '.editor_atto_content ::-moz-selection, .atto_text ::-moz-selection { ' +
                'background: #b3d4fc !important; ' +
                '}' +
                '</style>';
            $('head').append(css);
        }
    };

    /**
     * Prevenir capturas de pantalla
     * Implementa múltiples técnicas para bloquear screenshots
     */
    PreventCopyPaste.prototype.preventScreenshot = function() {
        var self = this;

        // 1. Crear overlay de protección para capturas
        this.createScreenshotOverlay();

        // 2. Detectar pérdida de foco de ventana (posible captura)
        $(window).on('blur', function() {
            self.showScreenshotProtection();
        });

        $(window).on('focus', function() {
            self.hideScreenshotProtection();
        });

        // 3. Detectar cambios de visibilidad de página
        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                self.showScreenshotProtection();
            } else {
                // Pequeño delay antes de ocultar para evitar capturas rápidas
                setTimeout(function() {
                    self.hideScreenshotProtection();
                }, 300);
            }
        });

        // 4. Bloquear tecla PrintScreen y combinaciones de captura
        $(document).on('keyup keydown', function(e) {
            var keyCode = e.keyCode || e.which;

            // PrintScreen key
            if (keyCode === 44) {
                e.preventDefault();
                self.showScreenshotWarning();
                // Limpiar el portapapeles si es posible
                self.clearClipboard();
                return false;
            }

            // Windows + Shift + S (Windows 10/11 Snipping Tool)
            if (e.metaKey && e.shiftKey && keyCode === 83) {
                e.preventDefault();
                self.showScreenshotWarning();
                return false;
            }

            // Windows + PrintScreen
            if (e.metaKey && keyCode === 44) {
                e.preventDefault();
                self.showScreenshotWarning();
                return false;
            }

            // Alt + PrintScreen
            if (e.altKey && keyCode === 44) {
                e.preventDefault();
                self.showScreenshotWarning();
                return false;
            }

            // Cmd + Shift + 3/4/5 (Mac screenshots)
            if (e.metaKey && e.shiftKey && (keyCode === 51 || keyCode === 52 || keyCode === 53)) {
                e.preventDefault();
                self.showScreenshotWarning();
                return false;
            }

            // Ctrl + Shift + S (algunas herramientas de captura)
            if (e.ctrlKey && e.shiftKey && keyCode === 83) {
                e.preventDefault();
                self.showScreenshotWarning();
                return false;
            }
        });

        // 5. Intentar bloquear Screen Capture API
        this.blockScreenCaptureAPI();

        // 6. Agregar CSS de protección anti-captura
        this.addScreenshotProtectionCSS();
    };

    /**
     * Crear overlay de protección
     */
    PreventCopyPaste.prototype.createScreenshotOverlay = function() {
        if ($('#screenshot-protection-overlay').length === 0) {
            var overlay = $('<div id="screenshot-protection-overlay"></div>');
            overlay.css({
                'position': 'fixed',
                'top': '0',
                'left': '0',
                'width': '100%',
                'height': '100%',
                'background': 'linear-gradient(135deg, #1B9E88 0%, #158C78 100%)',
                'z-index': '999999',
                'display': 'none',
                'justify-content': 'center',
                'align-items': 'center',
                'flex-direction': 'column',
                'color': '#ffffff',
                'font-family': 'Verdana, Arial, sans-serif',
                'text-align': 'center'
            });

            var content = $('<div class="protection-content"></div>');
            content.css({
                'padding': '40px',
                'background': 'rgba(255,255,255,0.1)',
                'border-radius': '16px',
                'backdrop-filter': 'blur(10px)'
            });

            content.html(
                '<div style="font-size: 64px; margin-bottom: 20px;">🛡️</div>' +
                '<h2 style="font-size: 24px; margin-bottom: 10px; font-weight: bold;">Contenido Protegido</h2>' +
                '<p style="font-size: 16px; opacity: 0.9;">Las capturas de pantalla están deshabilitadas en esta plataforma.</p>' +
                '<p style="font-size: 14px; opacity: 0.7; margin-top: 15px;">ISER - Instituto Superior de Educación Rural</p>'
            );

            overlay.append(content);
            $('body').append(overlay);
        }
    };

    /**
     * Mostrar protección de captura
     */
    PreventCopyPaste.prototype.showScreenshotProtection = function() {
        $('#screenshot-protection-overlay').css('display', 'flex');
    };

    /**
     * Ocultar protección de captura
     */
    PreventCopyPaste.prototype.hideScreenshotProtection = function() {
        $('#screenshot-protection-overlay').css('display', 'none');
    };

    /**
     * Mostrar advertencia de captura bloqueada
     */
    PreventCopyPaste.prototype.showScreenshotWarning = function() {
        var self = this;

        // Mostrar overlay temporalmente
        self.showScreenshotProtection();

        // Crear notificación toast si no existe
        if ($('#screenshot-warning-toast').length === 0) {
            var toast = $('<div id="screenshot-warning-toast"></div>');
            toast.css({
                'position': 'fixed',
                'bottom': '20px',
                'right': '20px',
                'background': '#EB4335',
                'color': '#ffffff',
                'padding': '15px 25px',
                'border-radius': '8px',
                'font-family': 'Verdana, Arial, sans-serif',
                'font-size': '14px',
                'z-index': '9999999',
                'box-shadow': '0 4px 12px rgba(0,0,0,0.3)',
                'display': 'none',
                'animation': 'slideIn 0.3s ease'
            });
            toast.html('⚠️ Las capturas de pantalla no están permitidas');
            $('body').append(toast);
        }

        // Mostrar toast
        $('#screenshot-warning-toast').fadeIn(300);

        // Ocultar después de 3 segundos
        setTimeout(function() {
            $('#screenshot-warning-toast').fadeOut(300);
            self.hideScreenshotProtection();
        }, 3000);
    };

    /**
     * Intentar limpiar el portapapeles
     */
    PreventCopyPaste.prototype.clearClipboard = function() {
        try {
            // Intentar sobrescribir el portapapeles con texto vacío
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText('').catch(function() {
                    // Silenciar error si no tiene permisos
                });
            }
        } catch (e) {
            // Silenciar error
        }
    };

    /**
     * Intentar bloquear Screen Capture API
     */
    PreventCopyPaste.prototype.blockScreenCaptureAPI = function() {
        // Intentar sobrescribir getDisplayMedia si está disponible
        if (navigator.mediaDevices && navigator.mediaDevices.getDisplayMedia) {
            var originalGetDisplayMedia = navigator.mediaDevices.getDisplayMedia.bind(navigator.mediaDevices);

            navigator.mediaDevices.getDisplayMedia = function() {
                console.warn('Screen capture blocked by ISER security policy');
                return Promise.reject(new Error('Screen capture is not allowed on this platform'));
            };
        }

        // Bloquear también getUserMedia para screen capture
        if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
            var originalGetUserMedia = navigator.mediaDevices.getUserMedia.bind(navigator.mediaDevices);

            navigator.mediaDevices.getUserMedia = function(constraints) {
                // Solo bloquear si se está solicitando captura de pantalla
                if (constraints && (constraints.video === true ||
                    (constraints.video && constraints.video.mediaSource === 'screen'))) {
                    // Permitir cámara/micrófono normal, bloquear screen capture
                    if (constraints.video && typeof constraints.video === 'object' &&
                        (constraints.video.mediaSource === 'screen' ||
                         constraints.video.mediaSource === 'window' ||
                         constraints.video.mediaSource === 'application')) {
                        console.warn('Screen capture blocked by ISER security policy');
                        return Promise.reject(new Error('Screen capture is not allowed'));
                    }
                }
                return originalGetUserMedia(constraints);
            };
        }
    };

    /**
     * Agregar CSS adicional para protección anti-captura
     */
    PreventCopyPaste.prototype.addScreenshotProtectionCSS = function() {
        if ($('#screenshot-protection-css').length === 0) {
            var css = '<style id="screenshot-protection-css">' +
                '/* Animación para toast de advertencia */' +
                '@keyframes slideIn {' +
                '    from { transform: translateX(100%); opacity: 0; }' +
                '    to { transform: translateX(0); opacity: 1; }' +
                '}' +

                '/* Protección adicional durante impresión */' +
                '@media print {' +
                '    body * {' +
                '        visibility: hidden !important;' +
                '    }' +
                '    body::before {' +
                '        content: "Impresión no permitida - ISER";' +
                '        visibility: visible !important;' +
                '        position: fixed;' +
                '        top: 50%;' +
                '        left: 50%;' +
                '        transform: translate(-50%, -50%);' +
                '        font-size: 24px;' +
                '        font-family: Verdana, Arial, sans-serif;' +
                '        color: #1B9E88;' +
                '    }' +
                '}' +

                '/* Marca de agua sutil para disuadir capturas */' +
                'body::after {' +
                '    content: "";' +
                '    position: fixed;' +
                '    top: 0;' +
                '    left: 0;' +
                '    width: 100%;' +
                '    height: 100%;' +
                '    pointer-events: none;' +
                '    z-index: 99998;' +
                '    background: repeating-linear-gradient(' +
                '        45deg,' +
                '        transparent,' +
                '        transparent 200px,' +
                '        rgba(27, 158, 136, 0.01) 200px,' +
                '        rgba(27, 158, 136, 0.01) 400px' +
                '    );' +
                '}' +
                '</style>';
            $('head').append(css);
        }
    };

    return {
        init: function() {
            // Esperar a que el DOM esté listo antes de inicializar
            $(document).ready(function() {
                return new PreventCopyPaste();
            });
        }
    };
});
