import { useState } from "react";
import {
    Form,
    Button,
    Card,
    Container,
    Row,
    Col,
    InputGroup,
    ToastContainer,
} from "react-bootstrap";
import { useNavigate } from "react-router-dom";
import style from "./login.module.css";
import { MdEmail } from "react-icons/md";
import { IoMdLock } from "react-icons/io";
import { FaEye } from "react-icons/fa";
import { FaEyeSlash } from "react-icons/fa";
import Api from "../../api/api";
import { toast } from "react-toastify";
function Login() {
    const [email, setEmail] = useState("")
    const [senha, setSenha] = useState("")
    const [verSenha, setVerSenha] = useState(false)
    const navigate = useNavigate()


    const fazerLogin = async (e, dados) => {
        e.preventDefault()
        

        try{
            const res = await Api.post('/usuario/login', {email, senha})
        
            if(res.status === 200){
                localStorage.setItem('token', res.data.token)
                navigate('/')
            }
        }catch(err){
            console.log(err)
            toast.error(err.response.data.mensagem || 'Erro ao fazer login', {
                position: "top-right",
                autoClose: 3000,
            });
        }
    }

    return (
        <>
            <div className={style.loginWrapper}>
                <Container >
                    <Row className="justify-content-center align-items-center vh-100">
                        <Col md={6} lg={4}>
                            <Card className={`border-0 shadow-lg ${style.loginCard}`}>
                                <Card.Body className="p-5">
                                    <div className="text-center mb-4">
                                        <h2 className="fw-bold">Bem-vindo</h2>
                                        <p className="text-muted">Acesse sua conta</p>
                                    </div>

                                    <Form onSubmit={fazerLogin}>
                                        <Form.Group className="mb-3">
                                            <Form.Label>E-mail</Form.Label>
                                            <InputGroup>
                                                <InputGroup.Text className="bg-white border-end-0">
                                                    <MdEmail />
                                                </InputGroup.Text>
                                                <Form.Control
                                                    type="email"
                                                    placeholder="nome@email.com"
                                                    className="border-start-0 ps-0"
                                                    value={email}
                                                    onChange={(e) => setEmail(e.target.value)}
                                                    required
                                                />
                                            </InputGroup>
                                        </Form.Group>

                                        <Form.Group className="mb-4">
                                            <Form.Label>Senha</Form.Label>
                                            <InputGroup>
                                                <InputGroup.Text className="bg-white border-end-0">
                                                    <IoMdLock />
                                                </InputGroup.Text>
                                                <Form.Control
                                                    type={verSenha ? `text` : "password"}
                                                    placeholder="••••••••"
                                                    className="border-start-0 ps-0"
                                                    value={senha}
                                                    onChange={(e) => setSenha(e.target.value)}
                                                    required
                                                />
                                                <Button onClick={() => setVerSenha(!verSenha)}>
                                                    {verSenha ? <FaEye /> : <FaEyeSlash />}
                                                </Button>
                                            </InputGroup>
                                        </Form.Group>

                                        <Button type="submit" className={`btn ${style.btnLogin}`}>
                                            Entrar
                                        </Button>
                                    </Form>
                                </Card.Body>
                            </Card>
                            <p className="text-center mt-3 text-white-50">
                                © 2026 Senac Wedding - Todos os direitos reservados.
                            </p>
                        </Col>
                    </Row>
                </Container>
            </div>
            <ToastContainer />
        </>
    )
}


export default Login;