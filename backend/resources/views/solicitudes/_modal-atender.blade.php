<div class="modal fade" id="atender{{ $sol->id }}" tabindex="-1" aria-labelledby="atenderLabel{{ $sol->id }}">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('solicitudes.atender', $sol->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="atenderLabel{{ $sol->id }}">Atender solicitud #{{ $sol->id }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small mb-3">
                        Se descontará inventario, el estado pasará a <strong>atendida</strong> y quedará en bitácora.
                        @if($emailsNotificacionHabilitados ?? true)
                            Se enviará <strong>copia obligatoria a TI</strong>.
                            @if($despachoDestinatarioHabilitado ?? true)
                                Si indica un correo abajo, esa persona recibirá el aviso de que la solicitud fue atendida.
                            @else
                                El aviso manual al solicitante está deshabilitado en configuración (solo copia a TI).
                            @endif
                        @else
                            Los correos están deshabilitados en configuración (solo registro en sistema).
                        @endif
                    </p>
                    @if(($emailsNotificacionHabilitados ?? true) && ($despachoDestinatarioHabilitado ?? true))
                        <div class="mb-3">
                            <label for="correo_destinatario_{{ $sol->id }}" class="form-label">Correo para aviso de solicitud atendida</label>
                            <input type="email"
                                   name="correo_destinatario"
                                   id="correo_destinatario_{{ $sol->id }}"
                                   class="form-control"
                                   maxlength="255"
                                   placeholder="ejemplo@correo.com"
                                   value="{{ old('correo_destinatario') }}"
                                   autocomplete="email">
                            <div class="form-text">
                                Ingrese manualmente el correo de quien debe recibir el aviso (solicitante u otro contacto).
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
                                  class="form-control"
                                  rows="3"
                                  maxlength="500"
                                  placeholder="Ej.: Puede recoger el material en bodega TI.">{{ old('nota_despacho') }}</textarea>
                        @if($emailsNotificacionHabilitados ?? true)
                            <div class="form-text">La nota se incluye en el aviso al correo indicado arriba y en la copia a TI.</div>
                        @endif
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Confirmar atención</button>
                </div>
            </form>
        </div>
    </div>
</div>
