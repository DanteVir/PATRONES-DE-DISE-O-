package com.respositorio.tareas.gestor_tareas.service;
import org.springframework.stereotype.Service;
import com.respositorio.tareas.gestor_tareas.model.Tarea;
import com.respositorio.tareas.gestor_tareas.repository.TareaRepository;

import java.util.List;
import java.util.Optional;

@Service
public class TareaService {
/* FACADE: Oculta la complejidad del acceso a datos y operaciones, actuando como fachada para los controladores. */
    private final TareaRepository tareaRepository;

    public TareaService(TareaRepository tareaRepository) {
        this.tareaRepository = tareaRepository;
    }

    public List<Tarea> listarTareas() {
        return tareaRepository.findAll();
    }

    public Tarea agregarTarea(Tarea tarea) {
        return tareaRepository.save(tarea);
    }

    public Optional<Tarea> obtenerTareaPorId(Long id) {
        return tareaRepository.findById(id);
    }

    public Tarea actualizarTarea(Long id, Tarea nuevaTarea) {
        return tareaRepository.findById(id).map(tarea -> {
            tarea.setDescripcion(nuevaTarea.getDescripcion());
            tarea.setCompletada(nuevaTarea.isCompletada());
            return tareaRepository.save(tarea);
        }).orElseThrow(() -> new RuntimeException("Tarea no encontrada"));
    }

    public void borrarTarea(Long id) {
        tareaRepository.deleteById(id);
    }
}

