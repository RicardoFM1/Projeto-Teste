import { useEffect, useState } from "react";
import { Button, Form, Modal, Stack } from "react-bootstrap";
import style from "./modalMesa.module.css";

function MesaModal({ dados, handleClose, onSubmit, show }) {
  const [formData, setFormData] = useState({
    capacidade: "",
    restricao: "",
  });

  const [editando, setEditando] = useState(false);

  useEffect(() => {
    if (dados) {
      setFormData(dados);
      setEditando(true);
    } else {
      setFormData({
        capacidade: "",
        restricao: "",
      });
      setEditando(false);
    }
  }, [dados, show]);

  const handleChange = (e) => {
    const { name, value } = e.target;
    if (!name) return;

    setFormData((prev) => ({ ...prev, [name]: value }));
  };

  const handleSubmit = (e) => {
    e.preventDefault();
    onSubmit(formData, editando);
  };

  return (
    <Modal style={{ zIndex: "1200" }} show={show} onHide={handleClose}>
      <Form className={style.modal} onSubmit={handleSubmit}>
        <Modal.Header closeButton>
          <Modal.Title>{editando ? "Editar Mesa" : "Criar Mesa"}</Modal.Title>
        </Modal.Header>
        <Modal.Body>
          <Stack gap={3}>
            <Form.Group>
              <Form.Label>Capacidade</Form.Label>
              <Form.Control
                type="number"
                name="capacidade"
                value={formData.capacidade}
                onChange={handleChange}
                required
              />
            </Form.Group>
            <Form.Group>
              <Form.Label>Restrição</Form.Label>
              <Form.Control
                type="text"
                name="restricao"
                value={formData.restricao}
                onChange={handleChange}
              />
            </Form.Group>
          </Stack>
        </Modal.Body>
        <Modal.Footer>
          <Button
            className="ignorar-fonte-btn"
            variant="secondary"
            onClick={handleClose}
          >
            Cancelar
          </Button>
          <Button className={`btn ${style.btnSalvar}`} type="submit">
            {editando ? "Salvar alterações" : "Criar"}
          </Button>
        </Modal.Footer>
      </Form>
    </Modal>
  );
}

export default MesaModal;
