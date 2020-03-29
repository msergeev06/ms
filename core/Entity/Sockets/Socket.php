<?php
/**
 * @package SHF "Доброжил"
 * @subpackage Ms\Core
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2020 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Sockets;

use Ms\Core\Entity\Errors\FileLogger;
use Ms\Core\Interfaces\ILogger;
use Ms\Core\Exception\SocketException;

/**
 * Класс Ms\Core\Entity\Sockets\Socket
 * Обертка для функций работы с сокетами
 */
class Socket
{
//<editor-fold defaultstate="collapse" desc=">>> Свойства">
	/**
	 * @var resource $resource Ресурс сокета
	 */
	protected $resource = null;

	/**
	 * @var ILogger $logger Объект логера
	 */
	protected $logger = null;
//</editor-fold>1

    /**
     * Конструктор класса Socket
     *
     * @param resource|null                    $resource
     * @param \Ms\Core\Interfaces\ILogger|null $logger
     *
     * @throws \Ms\Core\Exception\ArgumentException
     * @throws \Ms\Core\Exception\ArgumentOutOfRangeException
     */
	public function __construct ($resource = null, ILogger $logger = null)
	{
		if (!is_null($resource))
		{
			$this->setResource($resource);
		}
		if (!is_null($logger))
		{
			$this->logger = $logger;
		}
		else
		{
			$this->logger = new FileLogger('core');
		}

		return $this;
	}

//<editor-fold defaultstate="collapse" desc=">>> Динамические методы">
	//<editor-fold defaultstate="collapse" desc=">>> Getters and Setters">
	/**
	 * Возвращает действительный ресурс сокета
	 *
	 * @return resource
	 */
	public function getResource ()
	{
		return $this->resource;
	}

	/**
	 * Устанавливает действительный ресурс сокета
	 *
	 * @param resource $resource Ресурс
	 *
	 * @return Socket
	 */
	public function setResource ($resource): Socket
	{
		$this->resource = $resource;

		return $this;
	}

	/**
	 * Возвращает объект текущего логера
	 *
	 * @return ILogger
	 */
	public function getLogger (): ILogger
	{
		return $this->logger;
	}

	/**
	 * Устанавливает объект текущего логера
	 *
	 * @param ILogger $logger
	 *
	 * @return Socket
	 */
	public function setLogger (ILogger $logger): Socket
	{
		$this->logger = $logger;

		return $this;
	}

	//</editor-fold>

	//<editor-fold defaultstate="collapse" desc=">>> Обработка ошибок">
	/**
	 * Возвращает последнюю ошибку на сокете
	 *
	 * @return int
	 */
	public function getLastErrorCode ()
	{
		return socket_last_error();
	}

	/**
	 * Возвращает строку, описывающую ошибку сокета
	 *
	 * @return string
	 */
	public function getLastErrorString ()
	{
		return socket_strerror($this->getLastErrorCode());
	}

	/**
	 * Очищает ошибку на сокете или последний код ошибки
	 *
	 * @param Socket|null $socket
	 */
	public function clearError (Socket $socket = null)
	{
		if (!is_null($socket))
		{
			socket_clear_error($socket->getResource());
		}
		else
		{
			socket_clear_error();
		}
	}

	/**
	 * Возвращает последнее сообщение об ошибке сокетов
	 *
	 * @return string
	 */
	public function getLastErrorMessage ()
	{
		return 'SOCKET ERROR ['.$this->getLastErrorCode().']: ' . $this->getLastErrorString();
	}

	/**
	 * Добавляет в лог последнее сообщение об ошибке сокетов
	 *
	 * @return Socket
	 */
	public function addToLogLastErrorMessage ()
	{
		$this->logger->addMessage($this->getLastErrorMessage());

		return $this;
	}
	//</editor-fold>

	public function getType()
	{
		return $this->getOption(SOL_SOCKET, SO_TYPE);
	}

	/**
	 * Принимает соединение на сокете
	 *
	 * @uses socket_accept()
	 *
	 * @return Socket
	 * @throws SocketException
	 */
	public function accept ()
	{
		$resource = @socket_accept($this->resource);
		if ($resource === false)
		{
			$this->addToLogLastErrorMessage();
			throw new SocketException(__FILE__, __LINE__);
		}

		return $this;
	}

	/**
	 * Привязывает имя к сокету
	 *
	 * @param string $address Если сокет из семейства AF_INET,
	 *                        то параметр address должен быть IP-адресом в записи,
	 *                        разделённой точками (например, 127.0.0.1).
	 *                        Если сокет из семейства AF_UNIX,
	 *                        то параметр address - это путь к доменному сокету Unix
	 *                        (например, /tmp/my.sock).
	 * @param int    $port    Необязательный
	 *                        Параметр port используется, только когда имя привязывается к сокету AF_INET,
	 *                        и указывает порт, на котором будут слушаться соединения.
	 *
	 * @uses socket_bind()
	 *
	 * @return Socket
	 * @throws SocketException
	 */
	public function bind (string $address, int $port = 0)
	{
		$result = socket_bind($this->resource, $address, $port);
		if ($result === false)
		{
			$this->addToLogLastErrorMessage();
			throw new SocketException(__FILE__, __LINE__);
		}

		return $this;
	}

	/**
	 * Закрывает ресурс сокета
	 *
	 * @uses socket_close()
	 *
	 * @return Socket
	 */
	public function close ()
	{
		socket_close($this->resource);

		return $this;
	}

	/**
	 * Вычисляет размер буфера, который должен быть выделен для получения вспомогательных данных
	 *
	 * @param int $level
	 * @param int $type
	 * @param int $n
	 *
	 * @uses socket_cmsg_space()
	 *
	 * @return int
	 */
	public function cmsgSpace(int $level, int $type, int $n = 0)
	{
		return socket_cmsg_space($level, $type, $n);
	}

	/**
	 * Начинает соединение с сокетом
	 *
	 * @param string $address Параметр address может быть IPv4-адресом в записи,
	 *                        разделённой точками (например, 127.0.0.1),
	 *                        если параметр socket равен AF_INET,
	 *                        правильный IPv6-адрес (например, ::1),
	 *                        если включена поддержка IPv6 и параметр socket равен AF_INET6
	 *                        или путь к файлу доменного сокета Unix,
	 *                        если используется семейство сокетов AF_UNIX.
	 * @param int $port       Параметр port используется и обязателен только в том случае,
	 *                        если происходит соединение с сокетом AF_INET или AF_INET6,
	 *                        и он указывает порт на удалённом хосте,
	 *                        к которому должно быть создано соединение.
	 *
	 * @uses socket_connect()
	 *
	 * @return Socket
	 * @throws SocketException
	 */
	public function connect (string $address, int $port = 0)
	{
		$result = @socket_connect($this->resource, $address, $port);
		if ($result === false)
		{
			$this->addToLogLastErrorMessage();
			throw new SocketException(__FILE__, __LINE__);
		}

		return $this;
	}

	/**
	 * Открывает сокет на указанном порту для принятия соединений
	 *
	 * @param int $port    Порт, который нужно слушать на всех интерфейсах
	 * @param int $backlog Параметр backlog определяет максимальную длину,
	 *                     до которой может вырасти очередь ожидающих соединений.
	 *                     SOMAXCONN может быть передан как параметр backlog
	 *
	 * @uses socket_create_listen()
	 *
	 * @return Socket
	 * @throws SocketException
	 */
	public function createListen (int $port, int $backlog = 128)
	{
		$resource = @socket_create_listen($port, $backlog);
		if ($resource === false)
		{
			$this->addToLogLastErrorMessage();
			throw new SocketException (__FILE__, __LINE__);
		}

		$this->resource = $resource;

		return $this;
	}

	/**
	 * Создаёт пару неразличимых сокетов и сохраняет их в массиве
	 *
	 * @param int   $domain     Параметр domain определяет семейство протоколов, которое будет использоваться сокетом
	 * @param int   $type       Параметр type указывает тип коммуникации, которая будет использоваться сокетом
	 * @param int   $protocol   Параметр protocol устанавливает определённый протокол в указанном семействе протоколов domain,
	 *                          который будет использоваться при связи с полученными сокетами. Соответствующее значение может
	 *                          быть получено по имени при помощи функции getprotobyname(). Если требуемый протокол TCP или UDP,
	 *                          то соответствующие константы SOL_TCP и SOL_UDP также могут быть использованы
	 * @param Socket[] $fd      Ссылка на массив, в который будут вставлены два объекта сокетов (Socket)
	 *
	 * @uses socket_create_pair()
	 *
	 * @return Socket
	 * @throws SocketException
	 */
	public function createPair (int $domain, int $type, int $protocol, array &$fd)
	{
		$result = @socket_create_pair($domain, $type, $protocol, $fd);
		if ($result === false || !isset($fd[0]) || !isset($fd[1]))
		{
			$this->addToLogLastErrorMessage();
			throw new SocketException (__FILE__, __LINE__);
		}
		$fd = [new Socket($fd[0],$this->logger), new Socket($fd[1], $this->logger)];

		return $this;
	}

	/**
	 * Создаёт сокет (конечную точку для обмена информацией)
	 * Создаёт и возвращает ресурс сокета, также называемый как конечная точка обмена информацией.
	 * Типичное сетевое соединение состоит из двух сокетов, один из которых выполняет роль клиента,
	 * а другой выполняет роль сервера
	 *
	 * @param int $domain   Параметр domain определяет семейство протоколов, используемых сокетами:<br>
	 *                      AF_INET - Internet-протоколы IPv4. TCP и UDP - это стандартные протоколы этого семейства протоколов<br>
	 *                      AF_INET6 - Internet-протоколы IPv6. TCP и UDP - это стандартные протоколы этого семейства протоколов<br>
	 *                      AF_UNIX - Семейство протоколов для локального обмена данными. Высокая эффективность и низкие накладные
	 *                          расходы делают его отличным видом IPC (межпроцессного взаимодействия)
	 * @param int $type     Параметр type определяет тип обмена данными, который будет использоваться сокетом:<br>
	 *                      SOCK_STREAM - Обеспечивает последовательные, надёжные, полнодуплексные, байтовые потоки с установлением соединения.
	 *                          Может поддерживаться механизм передачи внеполосных (out-of-band) данных. Протокол TCP основан на этом типе сокетов<br>
	 *                      SOCK_DGRAM - Поддерживает датаграммы (ненадёжные сообщения без установления соединения фиксированной максимальной длины).
	 *                          Протокол UDP основан на этом типе сокетов<br>
	 *                      SOCK_SEQPACKET - редоставляет последовательную, надежную, двунаправленную, базирующуюся на соединениях передачу датаграмм
	 *                          с фиксированной максимальной длиной. Потребитель должен читать весь пакет целиком при каждой итерации чтения<br>
	 *                      SOCK_RAW - Предоставляет доступ по неподготовленному (raw) сетевому протоколу. Это специальный тип сокета может быть использован
	 *                          для ручного создания любого типа протокола. Стандартное использование этого типа сокетов - выполнение запросов ICMP (таких как ping)<br>
	 *                      SOCK_RDM - Предоставляет надежный уровень датаграм, не гарантирующий сохранение порядка. Скорее всего, это семейство протоколов
	 *                          не реализовано в вашей операционной системе
	 * @param int $protocol Параметр protocol указывает конкретный протокол в заданном семействе протоколов domain, который будет использоваться в обмене данными
	 *                      с созданным сокетом. Соответствующее значение может быть получено по имени при помощи функции getprotobyname(). Если желаемый
	 *                      протокол TCP или UDP, то соответствующие константы SOL_TCP и SOL_UDP также могут быть использованы:<br>
	 *                      icmp - ICMP (Internet Control Message Protocol, протокол межсетевых управляющих сообщений) используется преимущественно шлюзами и хостами
	 *                          для сообщения об ошибках в передаче датаграмм. Команда "ping" (присутствующая в большинстве современных операционных систем) -
	 *                          это пример использования ICMP-протокола<br>
	 *                      udp - UDP (User Datagram Protocol, протокол пользовательских датаграмм) - это протокол без установления соединения, ненадёжный, протокол
	 *                          с фиксированной длиной записей. Из-за этих аспектов, UDP требует минимального количества служебной информации<br>
	 *                      tcp - TCP (The Transmission Control Protocol, протокол управления передачей) - это надёжный, базирующийся на соединениях, потокоориентированный,
	 *                          полнодуплексный протокол. TCP гарантирует, что все пакеты данных будут получены в том порядке, в котором они были отправлены.
	 *                          Если какой-нибудь пакет каким-либо образом был утерян во время передачи данных, TCP будет автоматически передавать пакет повторно
	 *                          до тех пор, пока хост назначения не подтвердит этот пакет. В целях надежности и производительности, реализация протокола TCP сама
	 *                          выбирает подходящие границы октета нижележащего уровня обмена датаграммами. Таким образом, приложения, использующие TCP,
	 *                          должны предоставлять возможность частичной передачи записей
	 *
	 * @uses socket_create()
	 *
	 * @return Socket
	 * @throws SocketException
	 */
	public function create (int $domain, int $type, int $protocol)
	{
		$resource = @socket_create ($domain, $type, $protocol);
		if ($resource === false)
		{
			$this->addToLogLastErrorMessage();
			throw new SocketException (__FILE__, __LINE__);
		}

		$this->resource = $resource;

		return $this;
	}

	/**
	 * Экспортировать ресурс расширения сокета в поток, инкапсулирующий сокет
	 *
	 * @return resource
	 * @throws SocketException
	 */
	public function exportStream ()
	{
		$resource = @socket_export_stream ($this->resource);
		if ($resource === false)
		{
			$this->addToLogLastErrorMessage();
			throw new SocketException (__FILE__, __LINE__);
		}

		return $resource;
	}

	/**
	 * Получает опции потока для сокета
	 * Метод извлекает значение для опции, указанной параметром optname для заданного socket
	 *
	 * @param int $level    Параметр level указывает уровень протокола, на котором находится опция.
	 *                      Например, для получения опций на уровне сокета, должен использовать параметр level, равный SOL_SOCKET.
	 *                      Другие уровни, такие как TCP, можно использовать, указав номер протокола этого уровня.
	 *                      Номера протоколов можно найти с помощью функции getprotobyname()
	 * @param int $optname  Опции сокета. Список возможных значений смотрите в описании функции socket_get_option()
	 *
	 * @uses socket_get_option()
	 *
	 * @return mixed
	 * @throws SocketException
	 */
	public function getOption (int $level, int $optname)
	{
		$result = @socket_get_option ($this->resource, $level, $optname);
		if ($result === false)
		{
			$this->addToLogLastErrorMessage();
			throw new SocketException (__FILE__, __LINE__);
		}

		return $result;
	}

	/**
	 * Получает опции потока для сокета
	 * Псевдоним Socket::getOption()
	 *
	 * @param int $level    Параметр level указывает уровень протокола, на котором находится опция
	 * @param int $optname  Опции сокета
	 *
	 * @uses Socket::getOption()
	 *
	 * @return mixed
	 * @throws SocketException
	 */
	public function getOpt (int $level, int $optname)
	{
		return $this->getOption($level, $optname);
	}

	/**
	 * Запрашивает удалённую сторону указанного сокета, в результате может быть возвращен хост/порт или путь в файловой системе Unix, в зависимости от типа сокета
	 *
	 * @param string $address Если заданный сокет имеет тип AF_INET или AF_INET6, socket_getpeername() вернет удаленный IP-адрес в соответствующем
	 *                        формате ( например, 127.0.0.1 или fe80::1) в параметре address и, если необязательный параметр port присутствует,
	 *                        также связанный порт.<br>
	 *                        Если заданный сокет имеет тип AF_UNIX, метод вернет путь в файловой системе Unix (т.е. /var/run/daemon.sock)
	 *                        в параметр address
	 * @param int    $port    Если указан, то будет содержать порт, связанный с адресом address
	 *
	 * @uses socket_getpeername()
	 *
	 * @return Socket
	 * @throws SocketException
	 */
	public function getPeerName (string &$address, int &$port)
	{
		$result = @socket_getpeername ($this->resource, $address, $port);
		if ($result === false)
		{
			$this->addToLogLastErrorMessage();
			throw new SocketException (__FILE__, __LINE__);
		}

		return $this;
	}

	/**
	 * Запрашивает локальную сторону указанного сокета, в результате можно получить хост/порт или путь в файловой системе Unix, в зависимости от типа сокета
	 *
	 * @param string $addr Если заданный сокет имеет тип AF_INET или AF_INET6, socket_getpeername() вернет локальный IP-адрес в соответствующем
	 *                     формате ( например, 127.0.0.1 или fe80::1) в параметре address и, если необязательный параметр port присутствует,
	 *                     также связанный порт.<br>
	 *                     Если заданный сокет имеет тип AF_UNIX, метод вернет путь в файловой системе Unix (т.е. /var/run/daemon.sock)
	 *                     в параметр address
	 * @param int    $port Если указан, то будет содержать соответствующий порт
	 *
	 * @uses socket_getsockname()
	 *
	 * @return Socket
	 * @throws SocketException
	 */
	public function getSockName (string &$addr, int &$port)
	{
		$result = @socket_getsockname ($this->resource, $addr, $port);
		if ($result === false)
		{
			$this->addToLogLastErrorMessage();
			throw new SocketException (__FILE__, __LINE__);
		}

		return $this;
	}

	/**
	 * Импортировать поток
	 * Импортирует поток, который инкапсулирует сокет в ресурс расширения сокета
	 *
	 * @param resource $stream Ресурс поток для импорта
	 *
	 * @uses socket_import_stream()
	 *
	 * @return bool|resource|void|null
	 * @throws SocketException
	 */
	public function importStream ($stream)
	{
		$resource = @socket_import_stream ($stream);
		if (is_null($resource) || $resource === false)
		{
			$this->addToLogLastErrorMessage();
			throw new SocketException (__FILE__, __LINE__);
		}

		return $resource;
	}

	/**
	 * Прослушивает входящие соединения на сокете<br>
	 * После того, как сокет был создан при помощи метода Socket::create() и привязан к имени при помощи метода Socket::bind(),
	 * ему можно указать слушать входящие соединения <br>
	 * Метод применим только к сокетам типа SOCK_STREAM или SOCK_SEQPACKET
	 *
	 * @param int $backlog Максимум backlog входящих соединений будет помещено в очередь на обработку. Если запрос на соединение придет,
	 *                     когда очередь заполнена, клиент может получить ошибку ECONNREFUSED, или, если базовый протокол позволяет
	 *                     повторную передачу, запрос будет повторен
	 *
	 * @uses socket_listen()
	 *
	 * @return Socket
	 * @throws SocketException
	 */
	public function listen (int $backlog = 0)
	{
		$result = @socket_listen($this->resource, $backlog);
		if ($result === false)
		{
			$this->addToLogLastErrorMessage();
			throw new SocketException (__FILE__, __LINE__);
		}

		return $this;
	}

	/**
	 * Читает строку максимальную длину байт из сокета
	 *
	 * @param int $length Максимальное количество байт для чтения определено параметром length.
	 *                    Как вариант вы можете использовать \r, \n, или \0 для окончания чтения
	 *                    (в зависимости от параметра type, см ниже)
	 * @param int $type   Необязательный параметр type - это именованная константа:<br>
	 *                    PHP_BINARY_READ (По умолчанию) - используется системная функция recv(). Безопасно для чтения бинарных данных<br>
	 *                    PHP_NORMAL_READ - чтение останавливается на \n или \r
	 *
	 * @uses socket_read()
	 *
	 * @return string
	 * @throws SocketException
	 */
	public function read (int $length, int $type = PHP_BINARY_READ)
	{
		$result = @socket_read ($this->resource, $length, $type);
		if ($result === false)
		{
			$this->addToLogLastErrorMessage();
			throw new SocketException (__FILE__, __LINE__,'Socket operation failed');
		}

		return $result;
	}

	/**
	 * Получает данные из подсоединённого сокета<br>
	 * Метод получает len байт данных в буфер buf из сокета. Метод может быть использован для получения данных из подсоединённых сокетов.
	 * Дополнительно к этому, один или более флагов могут быть указаны для изменения поведения метода<br>
	 * Параметр buf передаётся по ссылке, так что он должен быть указан в виде переменной в списке аргументов. Данные, прочитанные из сокета
	 * методом, будут возвращены в параметре buf
	 *
	 * @param string $buf   Полученные данные будут переданы в переменную, указанную в параметре buf. Если происходит ошибка, если соединение
	 *                      сброшено, или если данные недоступны, параметр buf будет установлен в NULL
	 * @param int    $len   До len байт будет получено с удалённого хоста
	 * @param int    $flags Значение параметра flags может быть любой комбинацией следующих флагов, соединённых при помощи двоичного оператора OR (|)
	 *
	 * @uses socket_recv()
	 *
	 * @return int
	 * @throws SocketException
	 */
	public function recv (string &$buf, int $len, int $flags)
	{
		$result = @socket_recv ($this->resource, $buf, $len, $flags);
		if ($result === false)
		{
			$this->addToLogLastErrorMessage ();
			throw  new SocketException (__FILE__, __LINE__);
		}

		return $result;
	}

	/**
	 * Получает данные из сокета, независимо от того, подсоединён он или нет<br>
	 * Метод получает len байт данных в buf из адреса name на порту port (если сокет не типа AF_UNIX) используя сокет.
	 * Метод может быть использована для извлечения данных как из подключенных, так и из не подключенных сокетов.
	 * Дополнительно, один или более флагов могут быть указаны для того, чтобы изменить поведение метода.<br>
	 * Параметры name и port должны быть переданы по ссылке. Если сокет не ориентирован на соединение, name должен быть установлен
	 * как адрес интернет-протокола удаленного хоста, либо как путь к сокету UNIX. Если сокет не ориентирован на соединение,
	 * name должен быть NULL. Дополнительно, port должен содержать порт удаленного хоста для не подключенных сокетов типа AF_INET и AF_INET6
	 *
	 * @param string   $buf   Полученные данные будут переданы в переменную, указанную при помощи параметра buf
	 * @param int      $len   С удалённого хоста будет получено до len байт
	 * @param int      $flags Значение параметра flags может быть любой комбинацией следующих флагов, объединённых при помощи двоичного оператора OR (|) operator
	 * @param string $name    Если сокет типа AF_UNIX, name - это путь к файлу. В ином случае, для неподсоединённых сокетов, параметр name - это IP-адрес, удалённого хоста,
	 *                        или NULL, если сокет ориентирован по соединение
	 * @param int|null $port  Этот аргумент применим только к сокетам AF_INET и AF_INET6, и указывает удалённый порт, из которого будут получены данные.
	 *                        Если сокет ориентирован по соединение, port будет NULL
	 *
	 * @uses socket_recvfrom()
	 *
	 * @return int
	 * @throws SocketException
	 */
	public function recvFrom (string &$buf, int $len, int $flags, string &$name, int &$port = null)
	{
		$result = @socket_recvfrom ($this->resource, $buf, $len, $flags, $name, $port);
		if ($result === false)
		{
			$this->addToLogLastErrorMessage ();
			throw new SocketException (__FILE__, __LINE__);
		}

		return $result;
	}

	/**
	 * Прочитать сообщение
	 *
	 * @param array $message
	 * @param int   $flags
	 *
	 * @uses socket_recvmsg()
	 *
	 * @return int
	 * @throws SocketException
	 */
	public function recvMsg (array &$message, int $flags = 0)
	{
		$result = @socket_recvmsg ($this->resource, $message, $flags);
		if ($result === false)
		{
			$this->addToLogLastErrorMessage();
			throw new SocketException (__FILE__, __LINE__);
		}

		return $result;
	}

	/**
	 * Запускает системный вызов select() для заданных массивов сокетов с указанным тайм-аутом<br>
	 * Принимает массивы сокетов и ждет их изменения статуса. Те, кто знаком с сокетами BSD, обнаружат, что массивы с ресурсами сокетов
	 * на самом деле являются так называемыми наборами дескрипторов файлов. Наблюдаются три независимых массива ресурсов сокетов
	 *
	 * @param array $read    Сокеты, перечисленные в массиве read будут наблюдаться для просмотра, есть ли доступные символы для чтения
	 *                       ( точнее, чтобы видеть, не будет ли чтение блокироваться, в частности, ресурс сокета уже достиг конца файла,
	 *                       и в этом случае метод вернет строку с нулевой длиной)
	 * @param array $write   Сокеты, перечисленные в массиве write будут наблюдаться для просмотра, не будет ли запись блокироваться
	 * @param array $except  Сокеты, перечисленные в массиве except будут наблюдаться для исключений
	 * @param int $tv_sec    tv_sec и tv_usec вместе образуют параметр timeout. Параметр timeout - максимальный промежуток времени до возврата.
	 *                       tv_sec может быть нулём, заставляя метод к немедленному возврату. Это полезно для опроса. Если tv_sec равен NULL
	 *                       (нет тайм-аута), метод может блокироваться бесконечно
	 * @param int   $tv_usec см. выше
	 *
	 * @uses socket_select()
	 *
	 * @return int
	 * @throws SocketException
	 */
	public function select (array &$read, &$write, &$except, int $tv_sec, int $tv_usec = 0)
	{
		$result = @socket_select ($read, $write, $except, $tv_sec, $tv_usec);
		if ($result === false)
		{
			$this->addToLogLastErrorMessage ();
			throw new SocketException (__FILE__, __LINE__);
		}

		return $result;
	}

	/**
	 * Отправляет данные в подсоединённый сокет<br>
	 * Метод отправляет len байт в сокет из буфера buf
	 *
	 * @param string $buf   Буфер, содержащий данные, которые будут отправлены на удалённый хост
	 * @param int    $len   Число байт, которое будет отправлено на удалённый хост из буфера buf
	 * @param int    $flags Значение параметра flags может быть любой комбинацией следующих флагов, соединённых при помощи двоичного оператора OR (|)
	 *
	 * @uses socket_send()
	 *
	 * @return int
	 * @throws SocketException
	 */
	public function send (string $buf, int $len, int $flags)
	{
		$result = @socket_send ($this->resource, $buf, $len, $flags);
		if ($result === false)
		{
			$this->addToLogLastErrorMessage ();
			throw new SocketException (__FILE__, __LINE__);
		}

		return $result;
	}

	/**
	 * Отправить сообщение
	 *
	 * @param array $message
	 * @param int   $flags
	 *
	 * @uses socket_sendmsg()
	 *
	 * @return int
	 * @throws SocketException
	 */
	public function sendMsg (array $message, int $flags = 0)
	{
		$result = @socket_sendmsg ($this->resource, $message, $flags);
		if ($result === false)
		{
			$this->addToLogLastErrorMessage ();
			throw new SocketException (__FILE__, __LINE__);
		}

		return $result;
	}

	/**
	 * Отправляет сообщение в сокет, независимо от того, подсоединён он или нет<br>
	 * Метод отправляет len байт из буфера buf через сокет к порту port на адресе addr
	 *
	 * @param string $buf   Отправляемые данные будут взяты из буфера buf
	 * @param int    $len   len байт из буфера buf будет отправлено
	 * @param int    $flags Значение параметра flags может быть любой комбинацией следующих флагов, соединённых при помощи двоичного оператора OR (|)
	 * @param string $addr  IP-адрес удалённого хоста
	 * @param int    $port  port - это номер удалённого порта, по которому будут отправлены данные
	 *
	 * @uses socket_sendto()
	 *
	 * @return int
	 * @throws SocketException
	 */
	public function sendTo (string $buf, int $len, int $flags, string $addr, int $port = 0)
	{
		$result = @socket_sendto ($this->resource, $buf, $len, $flags, $addr, $port);
		if ($result === false)
		{
			$this->addToLogLastErrorMessage ();
			throw new SocketException (__FILE__, __LINE__);
		}

		return $result;
	}

	/**
	 * Устанавливает блокирующий режим на ресурсе сокета<br>
	 * Метод убирает флаг O_NONBLOCK с сокета.<br>
	 * Когда операция (например, получение, отправка, соединение, принятие соединения, ...) выполняется на блокирующем сокете,
	 * скрипт будет приостанавливать своё выполнение до тех пор, пока он не получит сигнал или возможность выполнить операцию
	 *
	 * @uses socket_set_block()
	 *
	 * @return Socket
	 * @throws SocketException
	 */
	public function setBlock ()
	{
		$result = @socket_set_block ($this->resource);
		if ($result === false)
		{
			$this->addToLogLastErrorMessage ();
			throw new SocketException (__FILE__, __LINE__);
		}

		return $this;
	}

	/**
	 * Устанавливает неблокирующий режим для файлового дескриптора fd<br>
	 * Метод устанавливает флаг O_NONBLOCK на сокете.<br>
	 * Когда операция (например, получение, отправка, соединение, принятие соединения, ...) выполняется на неблокирующем сокете,
	 * скрипт не будет приостанавливать своё исполнение до получения сигнала или возможности выполнить операцию.
	 * Если выполняемая операция должна привести к блокированию выполнения скрипта, то вместо этого вызываемая функция возвратит ошибку
	 *
	 * @uses socket_set_nonblock()
	 *
	 * @return Socket
	 * @throws SocketException
	 */
	public function setNonBlock ()
	{
		$result = @socket_set_nonblock ($this->resource);
//		msDebug($result);
		if ($result === false)
		{
			$this->addToLogLastErrorMessage ();
			throw new SocketException (__FILE__, __LINE__);
		}

		return $this;
	}

	/**
	 * Устанавливает опции для сокета<br>
	 * Метод устанавливает опцию указанную в параметре optname, на уровне протокола level, в значение, указанное параметром optval для сокета
	 *
	 * @param int $level    Параметр level указывает уровень протокола, на котором используется опция. Например, чтобы установить опции на уровне сокета,
	 *                      параметр level должен быть установлен в SOL_SOCKET. Другие уровни, такие как TCP, можно использовать, указав номер протокола
	 *                      этого уровня. Номер протоколов можно найти с помощью функции getprotobyname()
	 * @param int $optname  Возможные опции для сокета те же самые, как и для метода Socket::getOption()
	 * @param mixed $optval Значение опции
	 *
	 * @uses socket_set_option()
	 *
	 * @return Socket
	 * @throws SocketException
	 */
	public function setOption (int $level, int $optname, $optval)
	{
		$result = @socket_set_option ($this->resource, $level, $optname, $optval);
		if ($result === false)
		{
			$this->addToLogLastErrorMessage ();
			throw new SocketException (__FILE__, __LINE__);
		}

		return $this;
	}

	/**
	 * Устанавливает опции для сокета
	 * Псевдоним Socket::setOption()
	 *
	 * @param int $level    уровень протокола
	 * @param int $optname  опция для сокета
	 * @param mixed $optval Значение опции
	 *
	 * @uses Socket::setOption()
	 *
	 * @return Socket
	 * @throws SocketException
	 */
	public function setOpt (int $level, int $optname, $optval)
	{
		return $this->setOption($level, $optname, $optval);
	}

	/**
	 * Завершает работу сокета на получение и/или отправку данных<br>
	 * Метод позволяет вам остановить передачу поступающих, исходящих или всех данных (по умолчанию) через сокет
	 *
	 * @param int $how Значение параметра how может быть одним из следующих:<br>
	 *                 0 - Завершает чтение из сокета<br>
	 *                 1 - Завершает запись в сокет<br>
	 *                 2 - Завершает чтение и запись в сокет
	 *
	 * @uses socket_shutdown()
	 *
	 * @return Socket
	 * @throws SocketException
	 */
	public function shutdown (int $how = 2)
	{
		$result = @socket_shutdown ($this->resource, $how);
		if ($result === false)
		{
			$this->addToLogLastErrorMessage ();
			throw new SocketException (__FILE__, __LINE__);
		}

		return $this;
	}

	/**
	 * Запись в сокет<br>
	 * Метод записывает в сокет данные из указанного буфера buffer
	 *
	 * @param string $buffer Буфер, который будет записан
	 * @param int    $length Необязательный параметр length может указывать другое число байт, записываемых в сокет.
	 *                       Если это число больше, чем длина буфера, оно будет молча урезано до длины буфера
	 *
	 * @uses socket_write()
	 *
	 * @return int
	 * @throws SocketException
	 */
	public function write (string $buffer, int $length = 0)
	{
		$result = @socket_write ($this->resource, $buffer, $length);
		if ($result === false)
		{
			$this->addToLogLastErrorMessage ();
			throw new SocketException (__FILE__, __LINE__);
		}

		return $result;
	}
//</editor-fold>3
}