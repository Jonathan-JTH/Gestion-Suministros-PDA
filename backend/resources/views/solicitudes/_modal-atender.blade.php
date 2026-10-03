<div class="modal fade modal-pro" id="atender{{ $sol->id }}" tabindex="-1" aria-labelledby="atenderLabel{{ $sol->id }}">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('solicitudes.atender', $sol->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-semibold" id="atenderLabel{{ $sol->id }}">Atender solicitud #{{ $sol->id }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small mb-3">
                        Se descontará inventario, el estado pasará a <strong>atendida</strong> y quedará en bitácora.
                        @if($emailsNotificacionHabilitados ?? true)
                            Se enviará <strong>copia obligatoria a TI</strong>.
                            @if($despachoDestinatarioHabilitado ?? true)
                                Si indica un correo abajo, esa persona recibirá el aviso de atención.
                            @else
                                El aviso al solicitante está deshabilitado en configuración (solo copia a TI).
                            @endif
                        @else
                            Los correos están deshabilitados (solo registro en sistema).
                        @endif
                    </p>
                    @if(($emailsNotificacionHabilitados ?? true) && ($despachoDestinatarioHabilitado ?? true))
                        <div class="mb-3">
                            <label for="correo_destinatario_{{ $sol->id }}" class="form-label">Correo para aviso</label>
                            <input type="email"
                                   name="correo_destinatario"
                                   id="correo_destinatario_{{ $sol->id }}"
                                   class="form-control form-control-sm"
                                   maxlength="255"
                                   placeholder="ejemplo@correo.com"
                                   value="{{ old('correo_destinatario') }}"
                                   autocomplete="email">
                            <div class="form-text">
                                @if($sol->correoSolicitanteMostrar())
                                    Sugerencia: {{ $sol->correoSolicitanteMostrar() }}
                                @endif
                            </div>
                        </div>
                    @endif
                    <div class="mb-0">
                        <label for="nota_despacho_{{ $sol->id }}" class="form-label">Nota para el correo (opcional)</label>
                        <textarea name="nota_despacho"
                                  id="nota_despacho_{{ $sol->id }}"
                                  class="form-control form-control-sm"
                                  rows="3"
                                  maxlength="500"
                                  placeholder="Ej.: Puede recoger el material en bodega TI.">{{ old('nota_despacho') }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary btn-sm">Confirmar atención</button>
                </div>
            </form>
        </div>
    </div>
</div>
