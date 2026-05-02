import { useEffect, useState } from "react";
import { Button, Form, Modal, Stack } from "react-bootstrap";
import style from "./modalCheckin.module.css";

function CheckinModalEditar ({ data, handleClose, onSubmit, show, editando }) {
  const [formData, setFormData] = useState({
    convidado_idconvidado: ""
  });

  const [isEditing, setIsEditing] = useState(false);

  useEffect(() => {
    if (data) {
      setFormData(data);
      setIsEditing(true);
    } else {
      setFormData({
        convidado_idconvidado: ""
      });
      setIsEditing(false);
    }
  }, [data, show]);

  const handleChange = (e) => {
    const { name, value } = e.target;
    if (!name) return;

    setFormData((prev) => ({ ...prev, [name]: value }));
  };

  const handleSubmit = (e) => {
    e.preventDefault();
    const { id_checkin, ...restoDados } = formData;
    onSubmit(restoDados, isEditing);
  };

  return (
    <Modal style={{ zIndex: "10000" }} show={show} onHide={handleClose}>
      <Form onSubmit={handleSubmit}>
        <Modal.Header closeButton>
          <Modal.Title>Editar checkin</Modal.Title>
        </Modal.Header>
        <Modal.Body>
          <Stack gap={3}>
             <Form.Group>
              <Form.Label>Id do convidado</Form.Label>
              <Form.Control
                type="number"
                name="convidado_idconvidado"
                value={formData.convidado_idconvidado}
                onChange={handleChange}
                required
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
            {"Salvar alterações"}
          </Button>
        </Modal.Footer>
      </Form>
    </Modal>
  );
}

export default CheckinModalEditar